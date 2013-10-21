<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/7/13
 * Time: 10:17 PM
 * To change this template use File | Settings | File Templates.
 */

class Warfish extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

    public function index()
    {


    }

    public function rssText()
    {
        $this->load->model('wf');
        $allFeeds = $this->wf->readSubscribers();
        foreach ($allFeeds as $id => $personalFeed) {
            if (sizeof($personalFeed['sFeed']->channel->item) > 1) { //if just 1 item, it's not your turn in any games
                foreach ($personalFeed['sFeed']->channel->item as $entry) {
                    $title = (string)$entry->title[0];
                    // var_dump($title);
                    if (strpos($title, "list") !== false) { //this is the last entry,
                        break;
                    } else {
                        //It is your turn in at least 1 game!
                        $this->db->select('first_instance,reminder_sent,phone,provider');
                        $this->db->where(array('id' => $id));
                        $aTurn = $this->db->get('wf_rss')->row_array();
                        if ($aTurn['first_instance'] == null) {
                            //it wasn't your turn last check, so we'll now set the timer on 15 minutes
                            $this->db->query("UPDATE `wf_rss` SET  `first_instance` = NOW() WHERE  `wf_rss`.`id` =" . $id);
                        } else { //It's been your turn for at least 1 minute
                            $iFirst = strtotime($aTurn['first_instance']);
                            if ((time() - $iFirst) > 599 && $aTurn['reminder_sent'] == 0) { // 10 minutes in seconds and no reminder
                                $this->db->select('mms_gateway');
                                $this->db->where(array('provider' => $aTurn['provider']));
                                $aGateway = $this->db->get('ms_gateways')->row_array();


                                $this->load->library('email');
                                $emailAddress = $aTurn['phone'] . "@" . $aGateway['mms_gateway'];

                                //$config['protocol'] = 'sendmail';
                                //$config['mailpath'] = '/usr/sbin/sendmail';
                                //$config['charset'] = 'iso-8859-1';
                                $config['wordwrap'] = TRUE;
                                $config['protocol'] = 'smtp';
                                $config['smtp_host'] = 'mail.twonthink.com';
                                $config['smtp_user'] = 'warfish@twonthink.com';
                                $config['smtp_pass'] = WARFISH_EMAIL_PASSWORD; //defined in constants file
                                $config['smtp_port'] = 587;

                                $this->email->initialize($config);
                                $this->email->from('warfish@twonthink.com', 'warfish@twonthink.com');
                                $this->email->to($emailAddress);

                                //$this->email->subject('Email Test');
                                $this->email->message($entry->link . '
                               ' . "It's Your Turn Bro");


                                $this->email->send();


                                $this->db->query("UPDATE `wf_rss` SET  `reminder_sent` = 1 WHERE  `wf_rss`.`id` =" . $id);
                                echo $this->email->print_debugger();
                            }
                        }


                    }
                }
            } else {
                //It's not your turn in any games so update the reminder sent to 0 and the first_instance to null
                $this->db->query("UPDATE `wf_rss` SET  `reminder_sent` = 0, `first_instance`=NULL WHERE  `wf_rss`.`id` =" . $id);
            }
        }
    }
}

