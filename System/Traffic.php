<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Traffic
{
    protected $_firstDiscoveredBy       = false;

    protected $_trafficReport           = false;
    protected $_trafficReportLast24h    = false;
    protected $_trafficReportLast7d     = false;

    protected $_trafficBreakdown        = false;

    public function getFirstDiscoveredBy()
    {
        if($this->_firstDiscoveredBy === false)
        {
            $this->_firstDiscoveredBy = self::getModel('Models_Systems_Logs')->getFirstDiscoveredBy($this->getId());
        }

        return $this->_firstDiscoveredBy;
    }

    public function getTrafficReport()
    {
        if($this->_trafficReport === false)
        {
            $this->_trafficReport = self::getModel('Models_Systems_Logs')->getTrafficReport($this->getId());
        }

        return $this->_trafficReport;
    }

    public function getTrafficReportLast24h()
    {
        if($this->_trafficReportLast24h === false)
        {
            $this->_trafficReportLast24h = self::getModel('Models_Systems_Logs')->getTrafficReportLast24h($this->getId());
        }

        return $this->_trafficReportLast24h;
    }

    public function getTrafficReportLast7d()
    {
        if($this->_trafficReportLast7d === false)
        {
            $this->_trafficReportLast7d = self::getModel('Models_Systems_Logs')->getTrafficReportLast7d($this->getId());
        }

        return $this->_trafficReportLast7d;
    }

    public function getBreakdown()
    {
        if($this->_trafficBreakdown === false)
        {
            $breakdown = array();
            $traffic   = $this->getTrafficReportLast24h();

            if(!is_null($traffic))
            {
                foreach($traffic AS $ship)
                {
                    if(array_key_exists('user', $ship) && array_key_exists('refShip', $ship))
                    {
                        $user = \Component\User::getInstance($ship['user']);
                        $ship = $user->getShipById($ship['refShip']);

                        if(!is_null($ship))
                        {
                            $ship       = \EDSM_User_Ship::getInstance($ship);
                            $shipName   = $ship->getName();

                            if(array_key_exists($shipName, $breakdown))
                                $breakdown[$shipName] += 1;
                            else
                                $breakdown[$shipName]  = 1;
                        }
                    }
                }
            }

            if(count($breakdown) > 0)
            {
                ksort($breakdown);
                $this->_trafficBreakdown = $breakdown;
            }
            else
            {
                $this->_trafficBreakdown = null;
            }
        }

        return $this->_trafficBreakdown;
    }
}