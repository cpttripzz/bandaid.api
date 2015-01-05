<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 27/12/14
 * Time: 00:48
 */
namespace ZE\BABundle\Util;
class StringUtil
{

    static function after($searchItem, $searchSubject)
    {
        if (!is_bool(strpos($searchSubject, $searchItem))){
            return substr($searchSubject, strpos($searchSubject, $searchItem) + strlen($searchItem));
        }
    }

    static function after_last($searchItem, $searchSubject)
    {
        if (!is_bool(self::strrevpos($searchSubject, $searchItem))) {
            return substr($searchSubject, self::strrevpos($searchSubject, $searchItem) + strlen($searchItem));
        }
    }

    static function before($searchItem, $searchSubject)
    {
        return substr($searchSubject, 0, strpos($searchSubject, $searchItem));
    }

    static function before_last($searchItem, $searchSubject)
    {
        return substr($searchSubject, 0, self::strrevpos($searchSubject, $searchItem));
    }

    static function between($searchItem, $that, $searchSubject)
    {
        return self::before($that, self::after($searchItem, $searchSubject));
    }

    static function between_last($searchItem, $that, $searchSubject)
    {
        return self::after_last($searchItem, self::before_last($that, $searchSubject));
    }

    static function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false){
            return false;
        }
        else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }
}