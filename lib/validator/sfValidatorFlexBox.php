<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sfValidatorFlexBox
 *
 * @author ponkster
 */

class sfValidatorFlexBox extends sfValidatorBase
{

    protected function configure($options=array(), $messages=array())
    {
        $this->addOption('null_values', array('null',null));
    }

    protected function doClean($value)
    {
        print_r($value);exit;
    }

    public function isEmpty($value)
    {
        return false;
    }
}
?>
