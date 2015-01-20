<?php
namespace ZE\BABundle\Request;

class UserRegistrationRequest extends RequestAbstract
{
    public function __construct(array $options = array())
    {
        $columnConfig=array(
            'username'=>array('allowedTypes' => 'string','defined' =>true),
            'password'=>array('allowedTypes' => 'string','defined' =>true),
            'email'=>array('allowedTypes' => 'string','defined' =>true),
        );
        parent::__construct($options,$columnConfig,true);
    }
}
