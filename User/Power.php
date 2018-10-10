<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Power
{
    public function getPower()
    {
        $currentPower = $this->getIdentity('currentPower');
            
        if(!is_null($currentPower))
        {
            return (int) $currentPower;
        }
        
        return null;
    }
    
    public function getPowerLastUpdate()
    {
        return $this->getIdentity('lastPowerUpdate');
    }
}