<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 25/12/14
 * Time: 00:16
 */

namespace ZE\BABundle\Exception;



class InvalidFormException extends \RuntimeException
{
    protected $form;

    public function __construct($message, $form = null)
    {
        parent::__construct($message);
        $this->form = $form;
    }

    /**
     * @return array|null
     */
    public function getForm()
    {
        return $this->form;
    }
}