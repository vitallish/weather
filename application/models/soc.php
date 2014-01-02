<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/7/13
 * Time: 10:45 PM
 * To change this template use File | Settings | File Templates.
 */
class Soc extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

    }

    public function board_constuction($bsid)
    {
        $this->load->helper('vitaly');

        $this->db->select(array('id', 'position', 'row', 'column', 'adjacent'));
        $this->db->where(array('bs_id' => $bsid));
        $this->db->order_by('row', 'asc');
        $this->db->order_by('column', 'asc');
        $aMap = $this->db->get('soc_boardsetup')->result_array();

        $aMaxVal = $this->db->query('Select MAX(`row`) as maxRow, Max(`column`) as maxColumn from `soc_boardsetup` WHERE `bs_id` =' . $bsid)->row_array();
        $maxRow = $aMaxVal['maxRow'];
        $maxCol = $aMaxVal['maxColumn'];
        $countMapArray = 0;

        $this->db->select(array('resource_hex', 'number_tile'));
        $this->db->where(array('bs_id' => $bsid));
        $aPieces = $this->db->get('soc_piecesetup')->row_array();
        $aHex = expandArray(json_decode($aPieces['resource_hex']));
        $aTile = expandArray(json_decode($aPieces['number_tile']));
        shuffle($aHex);
        shuffle($aTile);
        $countHex = 0;
        $countTile = 0;


        $gameid = 1;
        $data = array();
        //$maxMapCount = sizeof($aMap);
        for ($r = 1; $r <= $maxRow; ++$r) {
            for ($c = 1; $c <= $maxCol; ++$c) {
                $class = "row_even";
                if ((abs($r) % 2) > 0) {
                    $class = "row_odd";
                }
                if ($c == 1) {
                    $class .= " start";
                }
                if (!array_key_exists($countMapArray, $aMap) || $c != $aMap[$countMapArray]['column']) {
                    //It's a blank piece
                    $data[$r][$c] = array(
                        'id' => 'r' . $r . 'c' . $c,
                        'class' => $class,
                        'type' => 'blank',
                        'src' => 'Hexagon - template.png'
                    );
                    continue;

                } elseif ($aMap[$countMapArray]['position'] < 0) { // It's water!
                    $adjacent = $aMap[$countMapArray]['adjacent'];
                    $custom = 'data-adjHex="' . $adjacent . '"';


                    $data[$r][$c] = array(
                        'id' => 'r' . $r . 'c' . $c,
                        'class' => $class,
                        'type' => 'water',
                        'src' => 'Hexagon water.png',
                        'custom' => $custom
                    );


                } else { //It's a land card
                    $curHex = $aHex[$countHex];
                    if ($curHex == 'desert') {
                        $curTile = '0';
                    } else {
                        $curTile = $aTile[$countTile];
                        ++$countTile;
                    }
                    ++$countHex;


                    $adjacent = $aMap[$countMapArray]['adjacent'];
                    $custom = 'data-adjHex="' . $adjacent . '"';

                    $data[$r][$c] = array(
                        'id' => 'r' . $r . 'c' . $c,
                        'class' => $class,
                        'type' => 'land',
                        'src' => 'Hexagon land.png',
                        'resource' => $curHex,
                        'number' => $curTile,
                        'custom' => $custom
                    );

                }
                /*
                //This is necessary to setup the adjacent in the database
                if (($r % 2) > 0) { //odd row
                    $adjacent = 'r' . ($r - 1) . 'c' . ($c - 1) . ' ';
                    $adjacent .= 'r' . ($r - 1) . 'c' . $c . ' ';
                    $adjacent .= 'r' . $r . 'c' . ($c + 1) . ' ';
                    $adjacent .= 'r' . ($r + 1) . 'c' . $c . ' ';
                    $adjacent .= 'r' . ($r + 1) . 'c' . ($c - 1) . ' ';
                    $adjacent .= 'r' . $r . 'c' . ($c - 1);

                } else { // even row
                    $adjacent = 'r' . ($r - 1) . 'c' . $c . ' ';
                    $adjacent .= 'r' . ($r - 1) . 'c' . ($c + 1) . ' ';
                    $adjacent .= 'r' . $r . 'c' . ($c + 1) . ' ';
                    $adjacent .= 'r' . ($r + 1) . 'c' . ($c + 1) . ' ';
                    $adjacent .= 'r' . ($r + 1) . 'c' . $c . ' ';
                    $adjacent .= 'r' . $r . 'c' . ($c - 1);
                }

                $this->db->where('id', $aMap[$countMapArray]['id']);
                $this->db->update('soc_boardsetup', array('adjacent' => $adjacent));
                //end of temporary section
                */


                $countMapArray++;


            }


        }

        $final = array('data' => $data,
            'maxCol' => $maxCol,
            'maxRow' => $maxRow);


        return $final;


    }


}