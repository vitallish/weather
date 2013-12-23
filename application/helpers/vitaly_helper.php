<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: Vitaly
 * Date: 10/4/13
 * Time: 6:55 AM
 * To change this template use File | Settings | File Templates.
 */
if ( ! function_exists('dtBreak'))
{
    function dtBreak($datetime)
    {
        if($datetime){
            $expDatetime = explode(' ',$datetime);
            $expDate = explode("-",$expDatetime[0]);
            $expTime = explode(":",$expDatetime[1]);
            $aDate=array('date'=>array(
                'full'=>$expDatetime[0],
                    'Y'=>$expDate[0],
                    'Y'=>$expDate[0],
                    'm'=>$expDate[1],
                    'd'=>$expDate[2]
                ),
                'time'=>array(
                    'full'=> $expDatetime[1],
                    'H'=>$expTime[0],
                    'm'=>$expTime[1],
                    's'=>$expTime[2]
                )
            );
        }else{
            $aDate=FALSE;
        }

        return $aDate;


    }

    function expandArray($aArray)
    {
        $output = array();

        foreach ($aArray as $type => $count) {
            for ($i = 0; $i < $count; ++$i) {
                $output[] = $type;
            }
        }

        return $output;
    }
}

