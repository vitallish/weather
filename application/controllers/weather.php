<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weather extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

    public function index()
    {
        $this->load->helper('form');
        $this->load->helper('url');
        $aOptions = array( 'hourly'=>'hourly',
            'daily'=>'daily');
        $sAttributes = 'id="select_type"';
        $data['select_type']=form_dropdown('select_type',$aOptions,'',$sAttributes);
        for($i=0;$i<36;$i++){
            $aOptionsHour[$i]=$i;
            if ($i<10){
                $aOptionsDay[$i]=$i; // Get at days while I'm at it.
            }
        }
        $sAttributesDay = 'id="select_day"';
        $sAttributesHour = 'id="select_hour"';
        $data['sel_hour']=form_dropdown('sel_hour',$aOptionsHour,'',$sAttributesHour);
        $data['sel_day']=form_dropdown('sel_day',$aOptionsDay,'',$sAttributesDay);
        $sAttributesData = 'id="select_data"';
        $aOptionsData = array('Temperature'=>'temp',
            'Precipitation'=>'pop');
        $data['sel_data']=form_dropdown('sel_data',$aOptionsData,'',$sAttributesData);


        $this->load->view('header_view');
        $this->load->view('weather_view', $data);
        $this->load->view('footer_view');
    }
    public function fetchWthrStats(){

        print_r($this->input->post());

    }

}

/* End of file homepage.php */
/* Location: ./application/controllers/weather.php */