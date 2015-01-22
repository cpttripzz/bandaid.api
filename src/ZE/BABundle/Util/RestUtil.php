<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 27/12/14
 * Time: 00:48
 */
namespace ZE\BABundle\Util;
class RestUtil
{

    static function formatRestResponse($boolValue,$message='')
    {
        $response = array('result' => $boolValue);
        if($message){
            $response['message'] = $message;
        }
        return $response;
    }


}