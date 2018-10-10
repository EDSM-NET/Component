<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Role
{
    use \Config\Secret\User\DistanceTeam;
    use \Config\Secret\User\EditorTeam;
    use \Config\Secret\User\GalacticMappingTeam;
    use \Config\Secret\User\GalnetTeam;
    use \Config\Secret\User\Patreon;
    use \Config\Secret\User\Streamer;
    
    public function getRole()
    {
        if($this->isValid() === false)
        {
            return 'guest';
        }

        if($this->getIdentity('valid') <= 0)
        {
            return 'guest';
        }

        if($this->isAdmin())
        {
            return 'admin';
        }

        if($this->isMaster())
        {
            return 'master';
        }

        return 'member';
    }
    
    public function isStreamer()
    {
        if(array_key_exists($this->getId(), self::$streamer))
        {
            return true;
        }
        
        return false;
    }
    
    public function isBenefactor()
    {
        // Guests
        if($this->getRole() == 'guest')
        {
            return false;
        }
        
        // Admin and team members
        if($this->isGalacticTeam() === true)
        {
            $this->giveBadge(9500);
            
            return true;
        }
        
        if(in_array($this->getId(), self::$patreon))
        {
            $this->giveBadge(9500);
            
            return true;
        }
        
        // Regular members
        if(is_null(self::$cache))
        {
            $bootstrap      = \Zend_Registry::get('Zend_Application');
            $cacheManager   = $bootstrap->getResource('cachemanager');
            self::$cache    = $cacheManager->getCache('database');
        }
        
        $cacheKey       = 'EDSM_USERS_DONATION_JSON';
        $donations      = self::$cache->load($cacheKey);
        $donationsFile  = APPLICATION_PATH . '/Config/Donation.json';
        
        if($donations === false || filemtime($donationsFile) > time())
        {
            $donations = \Zend_Json::decode(file_get_contents($donationsFile));
            
            self::$cache->save($donations, $cacheKey, array(), 43200); // 12h
        }
            
        foreach($donations AS $donation)
        {
            if($this->getId() == $donation['user'])
            {
                $this->giveBadge(9500);
                
                return true;
            }
        }
        
        return false;
    }
    
    public function isAdmin()
    {
        if($this->isValid() === false)
        {
            return false;
        }
            
        if($this->isMaster())
        {
            return true;
        }
        
        if($this->getIdentity('role') == 'admin')
        {
            return true;
        }
        
        return false;
    }
    
    public function canEditInformation()
    {
        if($this->isGalacticTeam())
        {
            return true;
        }
        
        if($this->isAdmin())
        {
            return true;
        }
            
        if($this->isMaster())
        {
            return true;
        }
            
        if(in_array($this->getId(), self::$editorTeam))
        {
            return true;
        }
        
        return false;
    }
    
    public function canEditDistances()
    {
        if($this->isAdmin())
        {
            return true;
        }
            
        if($this->isMaster())
        {
            return true;
        }
            
        if(in_array($this->getId(), self::$distanceTeam))
        {
            return true;
        }
        
        return false;
    }
    
    public function canEditGalnet()
    {
        if($this->isGalacticTeam())
        {
            return true;
        }
        
        if($this->isAdmin())
        {
            return true;
        }
            
        if($this->isMaster())
        {
            return true;
        }
            
        if(in_array($this->getId(), self::$galnetTeam))
        {
            return true;
        }
        
        return false;
    }
    
    public function isGalacticTeam()
    {
        if($this->isAdmin())
        {
            return true;
        }
            
        if($this->isMaster())
        {
            return true;
        }
            
        
        
        if(in_array($this->getId(), self::$galacticMappingTeam))
        {
            return true;
        }
        
        return false;
    }
    
    public function isMaster()
    {
        $masters = array(
            952,    // Anthor
        );
        
        if(in_array($this->getId(), $masters))
        {
            return true;
        }
        
        return false;
    }
}