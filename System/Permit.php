<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Permit
{
    protected $_permitLocked    = false;

    public function getPermitLocked()
    {
        if($this->_permitLocked === false)
        {
            $this->_permitLocked = self::getModel('Models_Systems_Permits')->getByRefSystem($this->getId());
        }

        return $this->_permitLocked;
    }
}