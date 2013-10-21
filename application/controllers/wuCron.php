<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class WuCron extends CI_Controller
{

    var $sWU_api = '087c8e632c757faf';
    var $sLocation = 'NY/New_York';


    public function __construct()
    {
        parent::__construct();
        $this->load->model('wu');
        $this->load->helper('vitaly');
        date_default_timezone_set('America/New_York');

    }

    public function index()
    {
        $this->forecast10day();
        $this->hourly();
        $this->conditions();
    }

    public function forecast10day()
    {
        $sWU_api = $this->sWU_api;
        $sLocation = $this->sLocation;
        $sWeather = 'forecast10day';

        $sJSON = file_get_contents("http://api.wunderground.com/api/" . $sWU_api . "/geolookup/" . $sWeather . "/q/" . $sLocation . ".json");
        $this->wu->forecast10day($sJSON);

        print_r('<pre>');
        print_r($sJSON);
    }

    public function hourly()
    {
        $sWU_api = $this->sWU_api;
        $sLocation = $this->sLocation;
        $sWeather = 'hourly';

        $sJSON = file_get_contents("http://api.wunderground.com/api/" . $sWU_api . "/geolookup/" . $sWeather . "/q/" . $sLocation . ".json");
        $this->wu->hourly($sJSON);

        print_r('<pre>');
        print_r($sJSON);
    }

    public function conditions()
    {
        $sWU_api = $this->sWU_api;
        $sLocation = $this->sLocation;

        $sWeather = 'conditions';
        $sJSON = file_get_contents("http://api.wunderground.com/api/" . $sWU_api . "/geolookup/" . $sWeather . "/q/" . $sLocation . ".json");
        $this->wu->conditions($sJSON);

        print_r('<pre>');
        print_r($sJSON);
    }

    public function diff_10day()
    {
        //select where POP or temp or actual ids is null
        // call proper functions to write it all to DB, one for IDs, one for diff, one for POP
        //goal to combine aggdate to one call for each of the three updateables.

        $this->db->where("date < '" . date('Y-m-d') . "' AND (diff_f_low IS NULL OR actual_ids IS NULL OR percent_pop IS NULL)");
        $this->db->select('date,diff_f_low,actual_ids,percent_pop');
        $oQuery = $this->db->get('wu_10day');
        /* $aRepeat helps keep track of which dates have already been looked at since they are repeated about 9 times
           and the the query captures all of them in the first go.
        */
        $aRepeat = array();
        foreach ($oQuery->result_array() as $row) {
            if (!in_array($row['date'], $aRepeat)) {

                $aRepeat[] = $row['date'];

                $bDiff_f_low = ($row['diff_f_low'] === NULL);
                $bActual_ids = ($row['actual_ids'] === NULL);
                $bPercent_pop = ($row['percent_pop'] === NULL);

                $bCompute = array('diff_f_low' => $bDiff_f_low,
                    'actual_ids' => $bActual_ids,
                    'percent_pop' => $bPercent_pop,
                    'check_complete' => TRUE);

                $this->wu->update10day($row['date'], $bCompute);
            }
        }
    }

    public function diff_36hour()
    {
        //find all datetimes which have been predicted
        $this->db->where("datetime < '" . date('Y-m-d H:i:s') . "' AND (diff_f_temp IS NULL OR actual_ids IS NULL OR percent_pop IS NULL)");

        $this->db->select('datetime,diff_f_temp,actual_ids,percent_pop');
        $oQuery = $this->db->get('wu_hourly');
        $aRepeat = array();

        foreach ($oQuery->result_array() as $key => $row) {
            if ($key == 0) { //skip the first line because not all data is available.
                continue;
            }
            if (!in_array($row['datetime'], $aRepeat)) {

                $aRepeat[] = $row['datetime'];

                $bDiff_f_temp = ($row['diff_f_temp'] === NULL);
                $bActual_ids = ($row['actual_ids'] === NULL);
                $bPercent_pop = ($row['percent_pop'] === NULL);

                $bCompute = array('diff_f_temp' => $bDiff_f_temp,
                    'actual_ids' => $bActual_ids,
                    'percent_pop' => $bPercent_pop,
                    'check_complete' => TRUE);

                $this->wu->update36hour($row['datetime'], $bCompute);
            }
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */