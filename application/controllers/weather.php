<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Weather extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->model('wu');
        $this->load->helper('vitaly');
    }

    public function index()
    {
        $this->load->helper('form');
        $this->load->helper('url');
        $aOptions = array('none' => '',
            'hourly' => 'hourly',
            'daily' => 'daily');
        $sAttributes = 'id="select_type"';
        $data['select_type'] = form_label("Select either hourly or daily predictions:", 'select_type') . form_dropdown('select_type', $aOptions, '', $sAttributes);

        for ($i = 0; $i < 36; $i++) {
            $aOptionsHour[$i] = $i;
            if ($i < 10) {
                $aOptionsDay[$i] = $i; // Get at days while I'm at it.
                $aOptionsPop[$i * 10] = $i * 10;
            }
        }
        $sAttributesPop = 'id = "select_pop"';
        $sAttributesDay = 'id="select_day"';
        $sAttributesHour = 'id="select_hour"';
        $data['sel_hour'] = form_label("Select how many hours ahead:", 'select_hour') . form_dropdown('sel_hour', $aOptionsHour, '', $sAttributesHour);
        $data['sel_day'] = form_label("Select how many days ahead:", 'select_day') . form_dropdown('sel_day', $aOptionsDay, '', $sAttributesDay);
        $data['sel_pop'] = form_label("Select percent of percipitation:", 'select_pop') . form_dropdown('sel_pop', $aOptionsPop, '', $sAttributesPop);
        $sAttributesData = 'id="select_data"';
        $aOptionsData = array('Temperature' => 'temp',
            'Precipitation' => 'pop');
        $data['sel_data'] = form_label("Select details on either POP or Temperature:", 'select_data') . form_dropdown('sel_data', $aOptionsData, '', $sAttributesData);


        $this->load->view('header_view');
        $this->load->view('weather_view', $data);
        $this->load->view('footer_view');
    }

    public function predictions()
    {
        /*
            This should make up the calender pick and choose etc. portion of the site.
            It's main job will first be to allow the user to either pick an hour or date in the future


        */
    }

    public function fetchWthrStats()
    {
        /*array $postInput = array(['selData']=>
                                    ['selType']=>
                                    ['selLen']=>    );


        */

        $postInput = $this->input->post();
        if ($postInput['selType'] == 'daily') {
            if ($postInput['selData'] == 'Temperature') {
                $data = $this->wu->tempDifferenceDay($postInput['selLen']);
                print_r($data);
            } else { //looking for preciptiation data
                $data = $this->wu->percentPopDay($postInput['selLen'], $postInput['selPop']);
                print_r($data);

            }


        } else { //looks like we are looking for hourly prediction stats
            if ($postInput['selData'] == 'Temperature') {
                $data = $this->wu->tempDifferenceHour($postInput['selLen']);
                print_r($data);

            } else { //looking for preciptiation data
                $data = $this->wu->percentPopHour($postInput['selLen'], $postInput['selPop']);
                print_r($data);

            }


        }

        if ($postInput['selData'] == 'Temperature') {


        } else { //looking for preciptiation data


        }


        print_r($postInput);


    }

}

/* End of file homepage.php */
/* Location: ./application/controllers/weather.php */