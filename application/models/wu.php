<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wu extends CI_Model {

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	
	function forecast10day($sJSON)
	{
		/*
			The 0th day is current forecast for the day. This is why it will be checked at
			7am to simulate checking the forecast for the rest of the day in the morning		
		*/
		$o10day = json_decode($sJSON); 
		$data = array('raw_json'=>$sJSON);
		$timezone = new DateTimeZone('America/New_York');
		for ($i=0;$i<10;$i++){
			$oForecastday = $o10day->{'forecast'}->{'simpleforecast'}->{'forecastday'}[$i];
			$dt = date_create_from_format('U',$oForecastday->{'date'}->{'epoch'},$timezone);
			//print_r($dt);
			$dt = date_timezone_set($dt,$timezone);
			
			$data['date'] =date_format($dt,'Y-m-d');
			$data['forecast_days'] = $i;
			$data['f_high'] = $oForecastday->{'high'}->{'fahrenheit'};
			$data['f_low'] = $oForecastday->{'low'}->{'fahrenheit'};
			$data['pop'] =$oForecastday->{'pop'};
			$data['qpf_allday_in'] =$oForecastday->{'qpf_allday'}->{'in'};
			$data['snow_allday_in'] =$oForecastday->{'snow_allday'}->{'in'};
			$data['conditions'] = $oForecastday->{'conditions'};
			if ($i==1){ //only write the raw json data to 1 day of the 10
				$data['raw_json']='';
			}
				
			$this->db->insert('wu_10day',$data);
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
		$data = array('raw_json'=>$sJSON);
		$timezone = new DateTimeZone('America/New_York');
		
		foreach( $oHourly->{'hourly_forecast'} as $hour=>$forecast){
			$dt = date_create_from_format('U',$forecast->{'FCTTIME'}->{'epoch'},$timezone);
			//print_r($dt);
			$dt = date_timezone_set($dt,$timezone);
			$data['forecast_hours'] = $hour;
			$data['datetime'] =date_format($dt,'Y-m-d H:i:s');
			$data['f_temp'] = $forecast->{'temp'}->{'english'};
			$data['condition'] = $forecast->{'condition'};
			$data['pop'] = $forecast->{'pop'};
			$data['qpf'] = $forecast->{'qpf'}->{'english'};
			$data['snow'] = $forecast->{'snow'}->{'english'};
			
			if ($hour == 1){ //only wite the raw json on the 0th hour
				$data['raw_json']='';
			}
			
			$this->db->insert('wu_hourly',$data);
		}	
	}
	function conditions($sJSON)
	{
		$oConditions = json_decode($sJSON)->{'current_observation'}; 
		$data = array('raw_json'=>$sJSON);
		$timezone = new DateTimeZone('America/New_York');
		$dt = date_create_from_format('U',$oConditions->{'observation_epoch'},$timezone);
		//print_r('<pre>');
		//print_r($oConditions);
		$dt = date_timezone_set($dt,$timezone);
		$data['datetime'] =date_format($dt,'Y-m-d H:i:s');
		$data['f_temp'] = $oConditions->{'temp_f'};
		$data['weather'] = $oConditions->{'weather'};
		$data['precip_today_in'] = $oConditions->{'precip_today_in'};
		$this->db->insert('wu_current',$data);
	}
	
}

/* End of file wu.php */
/* Location: ./application/models/ */