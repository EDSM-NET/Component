<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Mission
{
    protected $_acceptedMissions = false;
    
    public function getAcceptedMissions()
    {
        if($this->_acceptedMissions === false)
        {
            $this->_acceptedMissions = self::getModel('Models_Users_Missions')->getByRefUser($this->getId(), 'Accepted');
        }
        
        return $this->_acceptedMissions;
    }
    
    public function countAcceptedMissions()
    {
        $acceptedMissions = $this->getAcceptedMissions();
        
        if(!is_null($acceptedMissions))
        {
            return count($acceptedMissions);
        }
        
        return 0;
    }
}