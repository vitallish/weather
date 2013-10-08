<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/7/13
 * Time: 10:17 PM
 * To change this template use File | Settings | File Templates.
 */

class Warfish extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

    public function index(){



    }
    public function rssText(){
        $this->load->model('wf');

        print_r($this->wf->readSubscribers());
    }






}

