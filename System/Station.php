<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Station
{
    protected $_stations = false;

    public function getStations()
    {
        if($this->_stations === false)
        {
            $this->_stations = self::getModel('Models_Stations')->getByRefSystem($this->getId());
        }

        return $this->_stations;
    }

    public function getCountStations()
    {
        return count($this->getStations());
    }
}