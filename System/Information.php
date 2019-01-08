<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

use         Alias\System\Allegiance;
use         Alias\System\Government;
use         Alias\System\Economy;
use         Alias\System\State;
use         Alias\System\Security;

trait Information
{
    protected $_information = false;
    protected $_description = false;

    public function getInformation($key = null)
    {
        if($this->_information === false)
        {
            $this->_information = self::getModel('Models_Systems_Informations')->getByRefSystem($this->getId());
        }

        if(!is_null($key))
        {
            if(is_array($this->_information) && array_key_exists($key, $this->_information))
            {
                return $this->_information[$key];
            }

            return null;
        }

        return $this->_information;
    }


    public function getDescription()
    {
        if($this->_description === false)
        {
            $this->_description = self::getModel('Models_Systems_Descriptions')->getByRefSystem($this->getId());
        }

        if(!is_null(($this->_description)) && array_key_exists('description', $this->_description))
        {
            return $this->_description['description'];
        }

        return null;
    }



    public function getAllegiance()
    {
        $controllingFaction = $this->getFaction();

        if(!is_null($controllingFaction))
        {
            return $controllingFaction->getAllegiance();
        }

        return null;
    }

    public function getAllegianceName()
    {
        return Allegiance::get($this->getAllegiance());
    }



    public function getGovernment()
    {
        $controllingFaction = $this->getFaction();

        if(!is_null($controllingFaction))
        {
            return $controllingFaction->getGovernment();
        }

        return null;
    }

    public function getGovernmentName()
    {
        return Government::get($this->getGovernment());
    }



    public function getFaction($raw = false)
    {
        $refFaction = $this->getInformation('refFaction');

        if(!is_null($refFaction))
        {
            if($raw === true)
            {
                return $refFaction;
            }

            return \EDSM_System_Station_Faction::getInstance($refFaction);
        }

        return null;
    }

    public function getFactionState()
    {
        return $this->getInformation('factionState');
    }

    public function getFactionStateName()
    {
        return State::get($this->getFactionState());
    }



    public function getEconomy()
    {
        return $this->getInformation('economy');
    }

    public function getEconomyName()
    {
        return Economy::get($this->getEconomy());
    }



    public function getSecurity()
    {
        return $this->getInformation('security');
    }

    public function getSecurityName()
    {
        return Security::get($this->getSecurity());
    }



    public function getPopulation()
    {
        return $this->getInformation('population');
    }
}