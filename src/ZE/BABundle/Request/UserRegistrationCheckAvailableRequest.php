<?php
namespace ZE\BABundle\Request;

class UserRegistrationCheckAvailableRequest extends RequestAbstract
{
    public function __construct(array $options = array())
    {
        $columnConfig=array(
            'field'=>array('allowedTypes' => 'string','defined' =>true,'required'=>true),
            'value'=>array('allowedTypes' => 'string','defined' =>true,'required'=>true),
        );
        parent::__construct($options,$columnConfig,true);
    }
}
