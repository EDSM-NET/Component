<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Galnet
{
    protected $_galnet = false;

    public function getGalnet()
    {
        if($this->_galnet === false)
        {
            $this->_galnet = self::getModel('Models_Systems_Galnet')->getByRefSystem($this->getId());
        }

        return $this->_galnet;
    }
}