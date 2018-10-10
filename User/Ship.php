<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

use         \Alias\Ship\Type    AS ShipType;

trait Ship
{
    private $_ships = false;

    public function getShips()
    {
        if($this->_ships === false)
        {
            $this->_ships = self::getModel('Models_Users_Ships')->getByRefUser($this->getId());
        }

        return $this->_ships;
    }

    /**
     * Return the current ship ID from the game
     */
    public function getCurrentGameShipId()
    {
        $currentShipId = $this->getIdentity('currentShipId');
        if(!is_null($currentShipId))
        {
            return (int) $currentShipId;
        }

        return null;
    }

    /**
     * Return the current EDSM ship id from current game ship id
     */
    public function getCurrentShipId()
    {
        $gameShipId = $this->getCurrentGameShipId();
        $ships      = $this->getShips();

        if(!is_null($gameShipId))
        {
            foreach($ships AS $ship)
            {
                if($ship['refShip'] == $gameShipId)
                {
                    return (int) $ship['id'];
                }
            }
        }

        return null;
    }

    public function getCurrentGameShipIdLastUpdate()
    {
        return $this->getIdentity('lastCurrentShipUpdate');
    }

    /**
     * Convert game ship id to EDSM ship id
     */
    public function getShipById($gameShipId, $shipType = null, $dateUpdated = null)
    {
        $ships          = $this->getShips();
        $possibleShips  = array();

        foreach($ships AS $ship)
        {
            if($ship['refShip'] == $gameShipId)
            {
                // Check against type if provided
                if(!is_null($shipType) && array_key_exists('type', $ship))
                {
                    if($shipType == $ship['type'])
                    {
                        return (int) $ship['id'];
                    }

                    // Try the conversion
                    if(ShipType::getFromFd($shipType) == $ship['type'])
                    {
                        return (int) $ship['id'];
                    }
                }
                elseif(!is_null($dateUpdated) && array_key_exists('dateUpdated', $ship))
                {
                    if(strtotime($dateUpdated) <= strtotime($ship['dateUpdated']))
                    {
                        $possibleShips[] = (int) $ship['id'];
                    }
                }
                else
                {
                    return (int) $ship['id'];
                }
            }
        }

        // Return oldest possibility
        if(count($possibleShips) > 0)
        {
            $possibleShips = array_reverse($possibleShips);
            return $possibleShips[0];
        }
        elseif(!is_null($dateUpdated))
        {
            return $this->getShipById($gameShipId, $shipType);
        }

        return null;
    }

    public function countShips()
    {
        $ships = $this->getShips();

        if(!is_null($ships))
        {
            return count($ships);
        }

        return 0;
    }
}