<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WuCron extends CI_Controller {

	var $sWU_api = '087c8e632c757faf';
	var $sLocation = 'NY/New_York';
	
	public function __construct()
       {
            parent::__construct();
            $this->load->model('wu');
			
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
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */