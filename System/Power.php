<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Power
{
    protected $_powers = false;

    public function getPowers()
    {
        if($this->_powers === false)
        {
            $this->_powers = self::getModel('Models_Systems_Powerplay')->getByRefSystem($this->getId());
        }

        return $this->_powers;
    }
}