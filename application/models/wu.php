<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wu extends CI_Model
{
    public $bLocalComp;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->helper('url');
        $this->bLocalComp = FALSE;
        if (strstr(base_url(), 'localhost')) {
            $this->bLocalComp = TRUE;
        }
    }

    function forecast10day($sJSON)
    {
        /*
            The 0th day is current forecast for the day. This is why it will be checked at
            7am to simulate checking the forecast for the rest of the day in the morning
        */
        $o10day = json_decode($sJSON);
        if ($this->bLocalComp) {
            $data = array('raw_json' => $sJSON);
        }
        $timezone = new DateTimeZone('America/New_York');
        for ($i = 0; $i < 10; $i++) {
            $oForecastday = $o10day->{'forecast'}->{'simpleforecast'}->{'forecastday'}[$i];
            $dt = date_create_from_format('U', $oForecastday->{'date'}->{'epoch'}, $timezone);
            //print_r($dt);
            $dt = date_timezone_set($dt, $timezone);

            $data['date'] = date_format($dt, 'Y-m-d');
            $data['forecast_days'] = $i;
            $data['f_high'] = $oForecastday->{'high'}->{'fahrenheit'};
            $data['f_low'] = $oForecastday->{'low'}->{'fahrenheit'};
            $data['pop'] = $oForecastday->{'pop'};
            $data['qpf_allday_in'] = $oForecastday->{'qpf_allday'}->{'in'};
            $data['snow_allday_in'] = $oForecastday->{'snow_allday'}->{'in'};
            $data['conditions'] = $oForecastday->{'conditions'};
            if ($i == 1 && $this->bLocalComp) { //only write the raw json data to 1 day of the 10, on only on the local computer
                $data['raw_json'] = '';
            }

            $this->db->insert('wu_10day', $data);
        }

        //$this->db->insert('wu_10day',$data);
        //print_r("<pre>");
        //print_r($f_high);
        //print_r('<br>');
        //print_r($o10day);
    }

    function hourly($sJSON)
    {
        /*the 0th hour is the top of the hour coming up.
            It's best to run this 1 minute before the turn of the hour
            so the 0th hour comes up right away. This way when
            forecast_hours = 1, it is a prediction from 1 hour away

            This could be a problem if they update the forecast to something more
            accurate right at the end of the hour.
        */
        $oHourly = json_decode($sJSON);
        if ($this->bLocalComp) {
            $data = array('raw_json' => $sJSON);
        }
        $timezone = new DateTimeZone('America/New_York');

        foreach ($oHourly->{'hourly_forecast'} as $hour => $forecast) {
            $dt = date_create_from_format('U', $forecast->{'FCTTIME'}->{'epoch'}, $timezone);
            //print_r($dt);
            $dt = date_timezone_set($dt, $timezone);
            $data['forecast_hours'] = $hour;
            $data['datetime'] = date_format($dt, 'Y-m-d H:i:s');
            $data['f_temp'] = $forecast->{'temp'}->{'english'};
            $data['condition'] = $forecast->{'condition'};
            $data['pop'] = $forecast->{'pop'};
            $data['qpf'] = $forecast->{'qpf'}->{'english'};
            $data['snow'] = $forecast->{'snow'}->{'english'};

            if ($hour == 1 && $this->bLocalComp) { //only wite the raw json on the 0th hour and on local comp
                $data['raw_json'] = '';
            }

            $this->db->insert('wu_hourly', $data);
        }
    }

    function conditions($sJSON)
    {
        $oConditions = json_decode($sJSON)->{'current_observation'};
        if ($this->bLocalComp) {
            $data = array('raw_json' => $sJSON);
        }
        $timezone = new DateTimeZone('America/New_York');
        $dt = date_create_from_format('U', $oConditions->{'observation_epoch'}, $timezone);
        //print_r('<pre>');
        //print_r($oConditions);
        $dt = date_timezone_set($dt, $timezone);
        $data['datetime'] = date_format($dt, 'Y-m-d H:i:s');
        $data['f_temp'] = $oConditions->{'temp_f'};
        $data['weather'] = $oConditions->{'weather'};
        $data['precip_today_in'] = $oConditions->{'precip_today_in'};
        $this->db->insert('wu_current', $data);
    }

    function aggDay($sDate)
    {
        // Currently $sDate will need to be in the format 'Y-m-d' as is standard in mySQL
        // Input is a single day, the output is both daily and hourly aggregate data along with the IDs which lead to
        $this->db->where('date(`datetime`)', $sDate);
        $oQuery = $this->db->get('wu_current');
        $aData = array();
        $aData['day']['date'] = $sDate;
        $aData['day']['temp']['high'] = -150;
        $aData['day']['temp']['low'] = 150;
        $aData['day']['count'] = 0; //total measurements made that day
        $aData['day']['percip']['count'] = 0;
        $aData['day']['ref_ids'] = "";

        $aData['hour'] = array();

        //set up for checking precipitation
        $this->db->where('ref', 'wu_currentrain');
        $this->db->select('value');
        $sPercip = $this->db->get('constants')->row()->value;
        $aPercip = explode(",", $sPercip);


        if ($oQuery->num_rows() > 0) {
            $timezone = new DateTimeZone('America/New_York');
            //about 4/hour and 96/day
            foreach ($oQuery->result_array() as $row) {
                //set up particular hour to look at
                $iHour = (int)date_format(date_create_from_format('Y-m-d H:i:s', $row['datetime'], $timezone), 'H');

                if (array_key_exists($iHour, $aData['hour'])) { //This hour has been logged already
                    $aData['hour'][$iHour]['temp'] += $row['f_temp'];
                    $aData['hour'][$iHour]['count']++; //total measurements in an hour available, max 4
                    $aData['hour'][$iHour]['ref_ids'] .= (";" . $row['id']);
                } else { //create an entry if there isn't one already
                    $aData['hour'][$iHour]['temp'] = $row['f_temp'];
                    $aData['hour'][$iHour]['count'] = 1;
                    $aData['hour'][$iHour]['percip']['count'] = 0; //set the amount of times it rained during the hour to 0
                    $aData['hour'][$iHour]['ref_ids'] = $row['id'];
                }
                foreach ($aPercip as $rain) { //runs through each type of condition which qualifies as precipitation as defined in the database
                    if (stristr($row['weather'], $rain)) {
                        $aData['hour'][$iHour]['percip']['count']++; // add a count for a particular hour: max of 4
                        $aData['day']['percip']['count']++; //add a count for the whole day: max of 96
                        break; //make sure not to double count
                    }
                }
                $aData['day']['temp']['high'] = ($row['f_temp'] > $aData['day']['temp']['high'] ? $row['f_temp'] : $aData['day']['temp']['high']);
                $aData['day']['temp']['low'] = ($row['f_temp'] < $aData['day']['temp']['low'] ? $row['f_temp'] : $aData['day']['temp']['low']);
                $aData['day']['count']++; // total counts available in a day
            }

            //average temp for the hour.
            foreach ($aData['hour'] as $hour => $row) {
                $aData['hour'][$hour]['temp'] = $row['temp'] / (int)$row['count'];
                //also concatenate all the ids for the date
                $aData['day']['ref_ids'] .= ($aData['hour'][$hour]['ref_ids'] . ";");
            }
            //remove trailing semicolon:
            $aData['day']['ref_ids'] = substr($aData['day']['ref_ids'], 0, -1);


        } else {
            $aData = false;
        }

        return $aData;
    }

    function allCurrentDays()
    {
        /*
        */
        $oDates = $this->db->query("SELECT DATE(`datetime`) AS 'dates' FROM `wu_current` GROUP BY DATE(`datetime`) ORDER BY `datetime`");
        $aDates = $oDates->result_array();
        // $nDates = $oDates->num_results();
        return ($aDates);
    }

    function tempDifferenceDay($daysAdvance)
    {
        $query = 'SELECT AVG(ABS(diff_f_high)) AS mean_high, ';
        $query .= 'STDDEV(ABS(diff_f_high)) AS sd_high, ';
        $query .= 'AVG(ABS(diff_f_low)) AS mean_low, ';
        $query .= 'STDDEV(ABS(diff_f_low)) AS sd_low ';
        $query .= 'FROM wu_10day WHERE forecast_days=' . $daysAdvance;

        $temp = $this->db->query($query)->result_array();

        foreach ($temp[0] as $key => $val) {
            $round[$key] = round($val, 1);
        }

        return $round;
    }

    function tempDifferenceHour($hoursAdvance)
    {

        $query = 'SELECT AVG(ABS(diff_f_temp)) AS mean_diff, ';
        $query .= 'STDDEV(ABS(diff_f_temp)) AS sd_diff ';
        $query .= 'FROM wu_hourly WHERE forecast_hours=' . $hoursAdvance;

        $temp = $this->db->query($query)->result_array();

        foreach ($temp[0] as $key => $val) {
            $round[$key] = round($val, 1);
        }
        return $round;
    }

    function percentPOPDay($daysAdvance, $pop)
    {
        $minPercent = 0.05;

        $query = 'SELECT percent_pop ';
        $query .= 'FROM wu_10day WHERE pop=' . $pop . ' AND forecast_days=' . $daysAdvance;
        $temp = $this->db->query($query)->result_array();
        $countRain = 0;
        $countAlmost = 0;
        $countTotal = 0;
        foreach ($temp as $percent_pop) {
            $rainPercent = (float)$percent_pop['percent_pop'];
            //var_dump($rainPercent);
            if ($rainPercent > $minPercent) {
                $countRain++; //count as a rainy day
            } else if ($rainPercent > 0) {
                $countAlmost++; //not quite rainy enough
            } // if it's 0, not rainy in the slightest
            $countTotal++;
            //}
        }

        $aData = array('total' => $countTotal,
            'rain' => $countRain,
            'almost' => $countAlmost);

        return $aData;
    }

    function percentPOPhour($hoursAdvance, $pop)
    {
        $minPercent = 0.25;


        $query = 'SELECT percent_pop ';
        $query .= 'FROM wu_hourly WHERE pop=' . $pop . ' AND forecast_hours=' . $hoursAdvance;
        $temp = $this->db->query($query)->result_array();
        $countRain = 0;
        $countAlmost = 0;
        $countTotal = 0;

        foreach ($temp as $percent_pop) {
            //$aCurDate = $this->aggDay($date['date']);
            //if ($aCurDate) {
            //$iPercentRain = $aCurDate['day']['percip']['count'] / $aCurDate['day']['count']; //write iPercent
            $rainPercent = (float)$percent_pop['percent_pop'];
            //var_dump($rainPercent);
            if ($rainPercent > $minPercent) {
                $countRain++; //count as a rainy day
            } else if ($rainPercent > 0) {
                $countAlmost++; //not quite rainy enough
            } // if it's 0, not rainy in the slightest
            $countTotal++;
            //}

        }

        $aData = array('total' => $countTotal,
            'rain' => $countRain,
            'almost' => $countAlmost,
            'pop' => $pop,
            'query' => $query,
            'hoursAdvance' => $hoursAdvance);


        return $aData;


    }

    function update10day($sDate, $aOptions)
    {
        /* update10day will fill the temperature difference, percent_rain
         * and the actual_ids column in wu_10day.
         * $sDate is a string in the form of 'Y-m-d'
         * $aOptions
         * @$sDate
         */
        //$this->load->model('wu');

        if ($aAggdate = $this->aggDay($sDate)) {
            //Only write to those that have at least 24 hours of data
            if ($aOptions['check_complete']) {
                $bComplete = (sizeof($aAggdate['hour']) == 24);
            } else {
                $bComplete = TRUE;
            }
            //begin building query
            $sQuery = "UPDATE `wu_10day` SET";

            if ($aOptions['diff_f_low'] && $bComplete) {
                $sQuery .= "`diff_f_high` = (`f_high`-'" . $aAggdate['day']['temp']['high'] . "'), ";
                $sQuery .= "`diff_f_low` = (`f_low`-'" . $aAggdate['day']['temp']['low'] . "'), ";
            }
            if ($aOptions['actual_ids']) {
                $sQuery .= "`actual_ids` = '" . $aAggdate['day']['ref_ids'] . "', ";
            }
            if ($aOptions['percent_pop'] && $bComplete) {
                $percent = $aAggdate['day']['percip']['count'] / $aAggdate['day']['count'];
                $sQuery .= "`percent_pop` = " . $percent . ", ";
            }
            //trim last ", "

            $sQuery = substr($sQuery, 0, -2);
            $sQuery .= " WHERE `date` = '" . $sDate . "'";

            $this->db->query($sQuery);
        }
        return $aAggdate;
    }

    function update36Hour($sDatetime, $aOptions)
    {
        /* update10day will fill the temperature difference, percent_rain
         * and the actual_ids column in wu_10day.
         * $sDate is a string in the form of 'Y-m-d'
         * $aOptions
         * @$sDate
         */
        //$this->load->model('wu');
        $this->load->helper('vitaly');

        $aDTbreak = dtBreak($sDatetime);
        $sCurrentDate = $aDTbreak['date']['full'];
        $iCurrentTime = (int)$aDTbreak['time']['H'];

        $aAggdate = $this->aggDay($sCurrentDate);

        if ($aAggdate && array_key_exists($iCurrentTime, $aAggdate['hour'])) {
            //Only write to those that have at least 2 measurements in the hour
            $aCurrentHour = $aAggdate['hour'][$iCurrentTime];
            if ($aOptions['check_complete']) {
                $bComplete = ($aCurrentHour['count'] > 1);
            } else {
                $bComplete = TRUE;
            }

            //begin building query
            $sQuery = "UPDATE `wu_hourly` SET";

            if ($aOptions['diff_f_temp'] && $bComplete) {
                $sQuery .= "`diff_f_temp` = (`f_temp`-'" . $aCurrentHour['temp'] . "'), ";
            }
            if ($aOptions['actual_ids']) {
                $sQuery .= "`actual_ids` = '" . $aCurrentHour['ref_ids'] . "', ";
            }
            if ($aOptions['percent_pop'] && $bComplete) {
                $percent = $aCurrentHour['percip']['count'] / $aCurrentHour['count'];
                $sQuery .= "`percent_pop` = " . $percent . ", ";

            }
            //trim last ", "

            $sQuery = substr($sQuery, 0, -2);
            $sQuery .= " WHERE `datetime` = '" . $sDatetime . "'";
            if ($bComplete) {
                $this->db->query($sQuery);
                return $aCurrentHour;
            } else {
                return FALSE;
            }

        } else {
            return FALSE;
        }

    }


}

/* End of file wu.php */
/* Location: ./application/models/ */