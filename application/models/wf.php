<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/7/13
 * Time: 10:45 PM
 * To change this template use File | Settings | File Templates.
 */
class Wf extends CI_Model {
   function __construct()
    {
        // Call the Model constructor
        parent::__construct();

    }
    function readSubscribers(){
        $this->db->select('id,rss_id, phone, provider');
        $aQuery = $this->db->get('wf_rss')->result_array();

        foreach($aQuery as $row){
            $this->db->select('mms_gateway');
            $this->db->where(array('provider'=>$row['provider']));
            $aTempNumber = $this->db->get('ms_gateways')->row_array();
            $sFeed = file_get_contents("http://warfish.net/war/services/rss?t=t&rssid=".$row['rss_id']);

            $data[$row['id']]['email']=$row['phone'].'@'.$aTempNumber['mms_gateway'];
            $data[$row['id']]['sFeed']= new SimpleXMLElement($sFeed);
        }

        return $data;


    }




}

