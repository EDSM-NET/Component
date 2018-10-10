<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Exploration
{
    protected $_averageJumpDistance = false;
    protected $_averageFuelUsed     = false;
    
    public function getNbDistancesSubmitted()
    {
        $nbDistancesSubmitted = $this->getIdentity('nbDistancesSubmitted');
        
        if(!is_null($nbDistancesSubmitted))
        {
            $nbDistancesSubmitted = (int) $nbDistancesSubmitted;
            
            if($nbDistancesSubmitted >= 1000)
            {
                $this->giveBadge(9410);
                
                if($nbDistancesSubmitted >= 2000)
                {
                    $this->giveBadge(9415);
                }
                if($nbDistancesSubmitted >= 10000)
                {
                    $this->giveBadge(9420);
                }
            }
            
            return $nbDistancesSubmitted;
        }
        
        return 0;
    }
    
    public function getNbFlightLogs()
    {
        $nbFlightLogs = $this->getIdentity('nbFlightLogs');
        
        if(!is_null($nbFlightLogs))
        {
            return (int) $nbFlightLogs;
        }
        
        return 0;
    }
    
    public function getAverageJumpDistance()
    {
        if($this->_averageJumpDistance === false)
        {
            $this->_averageJumpDistance = 0;
            
            if($this->getRole() != 'guest')
            {
                $averageJumpDistance = self::getModel('Models_Systems_Logs')->getAverageJumpDistance($this->getId());

                if(!is_null($averageJumpDistance))
                {
                    $this->_averageJumpDistance = (float) $averageJumpDistance;
                }
            }
        }
        
        return $this->_averageJumpDistance;
    }
    
    public function getAverageFuelUsed()
    {
        if($this->_averageFuelUsed === false)
        {
            $this->_averageFuelUsed = 0;
            
            if($this->getRole() != 'guest')
            {
                $averageFuelUsed = self::getModel('Models_Systems_Logs')->getAverageFuelUsed($this->getId());
            
                if(!is_null($averageFuelUsed))
                {
                    $this->_averageFuelUsed = $averageFuelUsed;
                }
            }
        }
        
        return $this->_averageFuelUsed;
    }
}