<?php
/**
 * Created by PhpStorm.
 * User: tristanguckenberger
 * Date: 1/19/18
 * Time: 10:26 AM
 */

namespace InteractOne\LimitStates\Api\Data;




interface StateInterface
{
    /**
     * @return boolean
     */
    public function getBool();

    /**
     * @param boolean $state_allowed
     * @return void
     */
    public function setBool($state_allowed);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return void
     */
    public function setName($name);

}

