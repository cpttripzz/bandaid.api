<?php
namespace ZE\BABundle\Request;

class GetBandRequest extends RequestAbstract
{
    public function __construct(array $options = array())
    {
        $columnConfig=array(
            'bandSlug'=>array('allowedTypes' => 'string','defined' =>true),
        );
        parent::__construct($options,$columnConfig,true);
    }
}
