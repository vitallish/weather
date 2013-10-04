<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testpage extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wu');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('vitaly');
    }

    public function index()
    {
        $aDates = $this->wu->allCurrentDays();
        foreach($aDates as $value){
            $option[$value['dates']]=$value['dates'];
        }
        $sPage = "";

        $sAttributes = 'id="startDate"';

        $sPage.= form_label('Start Date:','startDate');
        $sPage.= form_dropdown('startDate',$option,'',$sAttributes);
        $sPage.=  '<br>';
        $sAttributes = 'id="endDate"';
        $sPage.=  form_label('End Date:','endDate');
        $sPage.=  form_dropdown('endDate',$option,'',$sAttributes);

        $sPage.=  '<pre>';
        //print_r ($this->wu->aggDay('2013-07-13'));
        // print_r($aDates);

        $sPage.= '<div id ="ajaxTest"></div>';
        $sPage.=  '</pre>';

        $data['plain']=$sPage;

        $this->load->view('header_view');
        $this->load->view('home_view',$data);
        $this->load->view('footer_view');
    }
    public function ajaxTest(){
        $data = $this->input->post();
        print_r( $this->wu->aggDay($data['date']));
    }
    public  function aDate(){
        print_r('<pre>');
        print_r($this->wu->aggDay('2013-07-11'));

    }
    public function dailyTemp(){
        print_r('<pre>');
        print_r($this->wu->tempDifferenceDay(5));


    }
    public function hourlyTemp(){
        print_r('<pre>');
        print_r($this->wu->tempDifferenceHour(5));

    }
    public function dailyPOP(){
        print_r('<pre>');
        print_r($this->wu->percentPOPDay(2,10));
    }
    public function helpDatetime(){
        print_r('<pre>');
        print_r(dtBreak('2013-07-01 01:12:13'));


    }

}

/* End of file testpage.php */
/* Location: ./application/controllers/testpage.php */