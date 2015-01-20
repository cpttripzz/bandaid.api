<?php
namespace ZE\BABundle\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class RequestAbstract
{
    protected $columnConfig=array(
        'page'=> array('allowedTypes'=> array('integer'),'default'=>1, 'required' =>false),
        'limit'=> array('allowedTypes'=> array('integer'),'default'=>50,'required' =>false),
    );
    public function __construct(array $options = array(),array $columnConfig=array(), $replaceParent=false)
    {
        if($replaceParent){
            $this->columnConfig = $columnConfig;
        } else {
            $this->columnConfig = array_merge($this->columnConfig,$columnConfig);
        }
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        foreach ($this->columnConfig as $name =>$columnConfig) {
            foreach($columnConfig as $columnConfigName => $columnConfigType){
                $resolver->setDefined($name,true);
                $resolver->{'set' . ucwords($columnConfigName)}($name,$columnConfigType);
            }
        }
    }
}
