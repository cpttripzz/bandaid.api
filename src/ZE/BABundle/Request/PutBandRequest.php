<?php
namespace ZE\BABundle\Request;

class PutBandRequest extends RequestAbstract
{
    public function __construct(array $options = array())
    {
        $columnConfig=array(
            'createdAt'=>array('allowedTypes' => 'string','defined' =>true),
            'updatedAt'=>array('allowedTypes' => 'string','defined' =>true),
            'name'=>array('allowedTypes' => 'string','defined' =>true),
            'description'=>array('allowedTypes' => 'string','defined' =>true),
            'id'=>array('allowedTypes' => 'integer','defined' =>true),
            'slug'=>array('allowedTypes' => 'string','defined' =>true),
            'genres'=>array('allowedTypes' => 'array','defined' =>true),
            'addresses'=>array('allowedTypes' => 'array','defined' =>true),
            'documents'=>array('allowedTypes' => 'array','defined' =>true),
            'musicians'=>array('allowedTypes' => 'array','defined' =>true),
            'type'=>array('allowedTypes' => 'string','defined' =>false),
        );
        parent::__construct($options,$columnConfig,true);
    }
}
