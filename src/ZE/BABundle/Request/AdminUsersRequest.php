<?php
namespace ZE\BABundle\Request;

class AdminUsersRequest extends RequestAbstract
{
    public function __construct(array $options = array())
    {
        $columnConfig=array('fromDate'=>array('allowedTypes' => 'Date','defined' =>true));
        parent::__construct($options,$columnConfig,false);
    }


}
