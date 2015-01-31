<?php
namespace ZE\BABundle\Request;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class RequestAbstract
{
    protected $columnConfig=array(
        'page'=> array('allowedTypes'=> array('numeric'),'default'=>1, 'required' =>false),
        'limit'=> array('allowedTypes'=> array('numeric'),'default'=>12,'required' =>false,'defined'=>true),
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
        if(isset($options['page'])) {
            $resolver->setNormalizer('page', function (Options $options, $value) {
                return (int)$value;
            });
        }
    }
}
