<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/7/13
 * Time: 10:17 PM
 * To change this template use File | Settings | File Templates.
 */

class Sochome extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('soc');

    }

    public function index()
    {
        $data['nice'] = '';
        $board = $this->soc->board_constuction(1);

        for ($r = 1; $r <= $board['maxRow']; ++$r) {

            for ($c = 1; $c <= $board['maxCol']; ++$c) {

                $currentTile = $board['data'][$r][$c];
                $id = $currentTile['id'];
                $src = "./../images/soc/" . $currentTile['src'];
                $class = $currentTile['class'] . ' ' . $currentTile['type'];

                if ($currentTile['type'] == 'land') {
                    $title = $currentTile['resource'] . ' ' . $currentTile['number'];
                    $custom = ' ' . $currentTile['custom'] . ' ';
                } else {
                    $title = $currentTile['type'];
                    $custom = '';
                }

                $data['nice'] .= '<img id="' . $id . '" class = "' . $class . '" title="' . $title . '"src="' . $src . '"' . $custom . '>';


            }

        }

        $this->load->view('soc/main_view', $data);


    }


}
