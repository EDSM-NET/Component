<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Locale
{
    public function getTimezone()
    {
        if(!empty($this->getIdentity('timezone')) && !is_null($this->getIdentity('timezone')))
        {
            return $this->getIdentity('timezone');
        }
        
        return 'UTC';
    }
    
    public function hasLocale()
    {
        if(!empty($this->getIdentity('language')) && !is_null($this->getIdentity('language')))
        {
            return true;
        }
        
        return false;
    }
    
    public function getLocale()
    {
        if($this->hasLocale() === true)
        {
            return $this->getIdentity('language');
        }
        
        return \Zend_Registry::get('appConfig')->resources->locale->default;
    }
}