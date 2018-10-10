<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Activity
{
    public function getDateLastActivity()
    {
        return $this->getIdentity('dateLastActivity');
    }
    
    public function getDateRegister()
    {
        return $this->getIdentity('dateRegister');
    }
    
    public function getDateLastLogin()
    {
        return $this->getIdentity('dateLastLogin');
    }
    
    public function getDateLastDocked()
    {
        $dateLastDocked = $this->getIdentity('dateLastDocked');
        if(!is_null($dateLastDocked))
        {
            return $dateLastDocked;
        }
        
        $dateRegister = $this->getDateRegister();
        if(strtotime($dateRegister) > strtotime('1 MONTH AGO'))
        {
            return $dateRegister;
        }
        
        //TODO: Check exploration dashboard new performance against large date ;)
        
        return date('Y-m-d H:i:s', strtotime('1 MONTH AGO'));
    }
    
    
    public function getLastKnownPosition()
    {
        if($this->getRole() != 'guest')
        {
            $lastKnownPosition = self::getModel('Models_Systems_Logs')->getUserLastKnownLocation($this->getId());
            
            return $lastKnownPosition;
        }
        
        return null;
    }
    
    
    public function isOnline()
    {
        $currentTime    = time();
        $timeToBeOnline = 300;
        
        // Check according to last activity
        $lastActivity   = $this->getDateLastActivity();
        if(!is_null($lastActivity))
        {
            if($currentTime - strtotime($lastActivity) <= $timeToBeOnline)
            {
                return true;
            }
            
            return false;
        }
        
        //TODO: Check against docked position and last position
        
        return false;
    }
}