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
		
		$this->load->view('welcomeTom', $data);
	
	}
	
}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */