<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Faction
{
    protected $_factionsReputation = array();

    public function getFactionReputation(\EDSM_System_Station_Faction $faction)
    {
        $factionId = $faction->getId();

        if(!array_key_exists($factionId, $this->_factionsReputation))
        {
            $this->_factionsReputation[$factionId] = self::getModel('Models_Users_Factions')->getByRefUserAndRefFaction($this->getId(), $factionId);
        }

        return $this->_factionsReputation[$factionId];
    }
}