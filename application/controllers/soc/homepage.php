<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/7/13
 * Time: 10:17 PM
 * To change this template use File | Settings | File Templates.
 */

class Homepage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('language');
        $this->load->model('soc'); 

    }

    public function index()
    {
        //validate form input
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true)
        {
            //check to see if the user is logging in
            //check for "remember me"
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
            {
                //if the login is successful
                //redirect them back to the home page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('/', 'refresh');
            }
            else
            {
                //if the login was un-successful
                //redirect them back to the login page
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('soc/homepage', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        }
        else
        {
            if($this->ion_auth->logged_in()){
                redirect('/');

            }
            //the user is not logging in so display the login page
            //set the flash data error message if there is one
            $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
            );

            $this->load->view('soc/login_view',$data);

        }

    }
    function create_user()
    {

        //validate form input
        $this->form_validation->set_rules('first_name',"First Name", 'required|xss_clean');
        $this->form_validation->set_rules('last_name', "Last Name", 'required|xss_clean');
        $this->form_validation->set_rules('email', "eMail", 'required|valid_email|is_unique[auth_users.email]');
        $this->form_validation->set_rules('phone', "Phone Number", 'xss_clean');
        $this->form_validation->set_rules('company', "Phone Company", 'xss_clean');
        $this->form_validation->set_rules('password', "Password", 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', "Password Confirmation", 'required');

        if ($this->form_validation->run() == true)
        {
            $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
            $email    = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $this->input->post('phone'),
            );
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data))
        {
            //check to see if we are creating the user
            //redirect them back to the admin page5
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("soc/homepage", 'refresh');
        }
        else
        {
            //display the create user form
            //set the flash data error message if there is one
            $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $data['first_name'] = array(
                'name'  => 'first_name',
                'id'    => 'first_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('first_name'),
            );
            $data['last_name'] = array(
                'name'  => 'last_name',
                'id'    => 'last_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('last_name'),
            );
            $data['email'] = array(
                'name'  => 'email',
                'id'    => 'email',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('email'),
            );
            $data['company'] = array(
                'name'  => 'company',
                'id'    => 'company',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('company'),
            );
            $data['phone'] = array(
                'name'  => 'phone',
                'id'    => 'phone',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('phone'),
            );
            $data['password'] = array(
                'name'  => 'password',
                'id'    => 'password',
                'type'  => 'password',
                'value' => $this->form_validation->set_value('password'),
            );
            $data['password_confirm'] = array(
                'name'  => 'password_confirm',
                'id'    => 'password_confirm',
                'type'  => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->load->view('soc/createuser_view', $data);
        }
    }


    public function creategame(){
        $board = $this->soc->board_constuction(1);
        $board_json = json_encode($board);

        $this->db->select_max('game_id');
        $query_row = $this->db->get('soc_gamestatus')->row_array();
        $newRow = $query_row['game_id']+1;

        $player1 = 'vitallish';
        $player2 = 'tkapusta';
        $player3 = 'kkrier';
        $player4 = 'djaw';

        $phase=-1;
        $gameturn = -1;
        $playerturn = 1;


        $data = array(
            'layout' => $board_json,
            'game_id' => $newRow,
            'player_turn'=>$playerturn,
            'game_turn' => $gameturn,
            'phase'=>$phase,
            'player1'=>$player1,
            'player2'=>$player2,
            'player3'=>$player3,
            'player4'=>$player4
        );

        $this->db->insert('soc_gamestatus',$data);
    }
    public function viewgame()
    {
        $this->db->select('layout');
        $this->db->where(array('game_id'=>11));
        $query_row = $this->db->get('soc_gamestatus')->row_array();
        $boardjson = $query_row['layout'];
        $board = json_decode($boardjson,true);

        $data['nice'] = '';
        for ($r = 1; $r <= $board['maxRow']; ++$r) {

            for ($c = 1; $c <= $board['maxCol']; ++$c) {

                $currentTile = $board['data'][$r][$c];
                $id = $currentTile['id'];
                $src = "./../../images/soc/" . $currentTile['src'];
                $class = $currentTile['class'] . ' ' . $currentTile['type'];
                $numberTile ='';
                if ($currentTile['type'] == 'land') {
                    $title = $currentTile['resource'] . ' ' . $currentTile['number'];
                    $custom = ' ' . $currentTile['custom'] . ' ';
                    $numberTile = $currentTile['numberTile'];
                } elseif ($currentTile['type'] == 'water') {
                    $title = $currentTile['type'];
                    $custom = ' ' . $currentTile['custom'] . ' ';
                    $numberTile = $currentTile['numberTile'];
                } else {
                    $title = $currentTile['type'];
                    $custom = '';
                }
                $data['nice'] .= '<div id="' . $id . '" class = "hexTile ' . $class . '" title="' . $title.'"'. $custom . '><img class="hexResource" src="' . $src . '">'.$numberTile.'</div>';


            }

        }

        $this->load->view('soc/test_view', $data);


    }


}
