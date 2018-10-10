<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Credit
{
    protected $_lastCredits = false;
    
    public function getCredits()
    {
        if($this->_lastCredits === false)
        {
            $credits = self::getModel('Models_Users_Credits')->getLastByRefUser($this->getId());
        
            // BADGES
            if(!is_null($credits) && array_key_exists('balance', $credits))
            {
                if($credits['balance'] >= 1001)
                {
                    $this->giveBadge(1000);
                }
                if($credits['balance'] >= 1000000)
                {
                    $this->giveBadge(1100);
                }
                if($credits['balance'] >= 1000000000)
                {
                    $this->giveBadge(1200);
                }
                if($credits['balance'] >= 4000000000)
                {
                    $this->giveBadge(1500);
                }
            }
            
            $this->_lastCredits = $credits;
        }
        
        return $this->_lastCredits;
    }
}