<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Station
{
    protected $_stations = false;

    public function getStations($raw = false)
    {
        if($raw === true)
        {
            return self::getModel('Models_Stations')->getByRefSystem($this->getId());
        }

        if($this->_stations === false)
        {
            $stations           = self::getModel('Models_Stations')->getByRefSystem($this->getId());
            $this->_stations    = array();

            foreach($stations AS $key => $station)
            {
                $station = \EDSM_System_Station::getInstance($station['id']);

                // Check mega ship status
                if($station->getType() == 12 && stripos($station->getName(), 'Rescue Ship') !== false)
                {
                    if(strtotime($station->getUpdateTime()) < strtotime('2 DAY AGO'))
                    {
                        continue;
                    }

                    // Check linked station?
                    $shipName = trim(str_ireplace('Rescue Ship - ', '', $station->getName()));

                    if(array_key_exists(($key - 1), $this->view->stations))
                    {
                        $testStation = $this->view->stations[($key - 1)];

                        if($shipName == $testStation->getName())
                        {
                            if($testStation->getEconomy() != 99)
                            {
                                continue;
                            }
                        }
                    }

                    if(array_key_exists(($key + 1), $stations))
                    {
                        $testStation = \EDSM_System_Station::getInstance($stations[($key + 1)]['id']);

                        if($shipName == $testStation->getName())
                        {
                            if($testStation->getEconomy() != 99)
                            {
                                continue;
                            }
                        }
                    }
                }

                $this->_stations[$key] = $station;
            }
        }

        return $this->_stations;
    }

    public function getCountStations()
    {
        return count($this->getStations());
    }
}