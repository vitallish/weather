<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Homepage extends CI_Controller {
	 public function __construct()
       {
            parent::__construct();
            // Your own constructor code
       }
	   
	public function index()
	{
		$data['hourly'] =($this->db->get('wu_hourly')->num_rows());
		$data['current'] =($this->db->get('wu_current')->num_rows());
		$data['f10day'] =($this->db->get('wu_10day')->num_rows());
		
		
		for ($i=0;$i<10;$i++){
			$query = 'SELECT AVG(ABS(diff_f_high)) AS mean_high, ';
			$query.= 'STDDEV(ABS(diff_f_high)) AS sd_high, ';
			$query.= 'AVG(ABS(diff_f_low)) AS mean_low, ';
			$query.= 'STDDEV(ABS(diff_f_low)) AS sd_low ';
			$query.= 'FROM wu_10day WHERE forecast_days='.$i;
		
			$temp =$this->db->query($query)->result_array();
			foreach ($temp[0] as $key=>$val){
				$round[$key]=round($val,1);
			}
			$data['prediction'][] =$round;
		//print_r($temp);
		}
		
		
		$this->load->view('welcomeTom', $data);
	
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */