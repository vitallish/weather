<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WuCron extends CI_Controller {

	var $sWU_api = '087c8e632c757faf';
	var $sLocation = 'NY/New_York';
	
	
	public function __construct()
       {
            parent::__construct();
            $this->load->model('wu');
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
		
		$sJSON = file_get_contents("http://api.wunderground.com/api/".$sWU_api."/geolookup/".$sWeather."/q/".$sLocation.".json");
		$this->wu->forecast10day($sJSON);	
		
		print_r('<pre>');
		print_r($sJSON);
	}
	public function hourly()
	{
		$sWU_api = $this->sWU_api;
		$sLocation = $this->sLocation;
		$sWeather = 'hourly';
		
		$sJSON = file_get_contents("http://api.wunderground.com/api/".$sWU_api."/geolookup/".$sWeather."/q/".$sLocation.".json");
		$this->wu->hourly($sJSON);
	
		print_r('<pre>');
		print_r($sJSON);
	}
	
	public function conditions()
	{
		$sWU_api = $this->sWU_api;
		$sLocation = $this->sLocation;
		
		$sWeather = 'conditions';
		$sJSON = file_get_contents("http://api.wunderground.com/api/".$sWU_api."/geolookup/".$sWeather."/q/".$sLocation.".json");
		$this->wu->conditions($sJSON);
	
		print_r('<pre>');
		print_r($sJSON);
	}
	public function diff_10day()
	{
		
		$this->db->where("date < '".date('Y-m-d')."' AND diff_f_low IS NULL");
		$this->db->select('date');
		$oQuery = $this->db->get('wu_10day');
		$aRepeat = array();
		foreach($oQuery->result_array() as $row){
			if(!in_array($row['date'],$aRepeat)){
				//print_r($row);
				$aRepeat[] = $row['date'];
				$aAggdate = $this->wu->aggDay($row['date']);
				//echo sizeof($aAggdate['hour']).' ';
				if($aAggdate && sizeof($aAggdate['hour'])==24){
					$sQuery = "UPDATE `wu_10day` ";
					$sQuery.="SET `diff_f_high` = (`f_high`-'".$aAggdate['day']['temp']['high']."'), "; 
					$sQuery.="`diff_f_low` = (`f_low`-'".$aAggdate['day']['temp']['low']."')";
					$sQuery.="WHERE `date` = '".$row['date']."'";
					print_r('<pre>');
					print_r($aAggdate);
					$this->db->query($sQuery);
				}
			}
		}
    }
        public function diff_36hour(){
            $this->db->where("datetime < '".date('Y-m-d H:i:s')."' AND diff_f_low IS NULL");
            $this->db->select('datetime');
            $oQuery = $this->db->get('wu_hourly');
            $aRepeat = array();
            foreach($oQuery->result_array() as $row){
                if(!in_array($row['datetime'],$aRepeat)){
                    //print_r($row);
                    $aRepeat[] = $row['date'];
                    $aAggdate = $this->wu->aggDay($row['date']);
                    //echo sizeof($aAggdate['hour']).' ';
                    if($aAggdate && sizeof($aAggdate['hour'])==24){
                        $sQuery = "UPDATE `wu_10day` ";
                        $sQuery.="SET `diff_f_high` = (`f_high`-'".$aAggdate['day']['temp']['high']."'), ";
                        $sQuery.="`diff_f_low` = (`f_low`-'".$aAggdate['day']['temp']['low']."')";
                        $sQuery.="WHERE `date` = '".$row['date']."'";
                        print_r('<pre>');
                        print_r($aAggdate);
                        $this->db->query($sQuery);
                    }
                }
            }



    }
				
				
		
		
		
		//print_r('<pre>');
		//print_r($oQuery->result_array());
		
		

	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */