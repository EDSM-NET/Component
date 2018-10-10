<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait HoloMe
{
    public function canUploadImage()
    {
        if($this->isAdmin())
        {
            return true;
        }
        
        if($this->getRole() != 'guest')
        {
            $nbDelete = 0;
            
            if(is_null(self::$cache))
            {
                $bootstrap      = \Zend_Registry::get('Zend_Application');
                $cacheManager   = $bootstrap->getResource('cachemanager');
                self::$cache    = $cacheManager->getCache('database');
            }
            
            $cacheKey           = 'EDSM_User_canUploadImage_' . $this->getId();
            $nbCurrentDelete    = self::$cache->load($cacheKey);
            
            if($nbCurrentDelete !== false)
            {
                $nbDelete += $nbCurrentDelete;
            }
            
            if($nbDelete >= 3)
            {
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function getImageFileName()
    {
        if($this->getRole() != 'guest')
        {
            $split  = str_split((string) $this->getId());
            $folder = '/img/users/';
            
            for($i = 0; $i <= 9; $i++)
            {
                if(array_key_exists($i, $split))
                {
                    $folder .= $split[$i] . '/';
                    
                    if(!is_dir(PUBLIC_PATH . $folder))
                    {
                        mkdir(PUBLIC_PATH . $folder);
                    }
                }
            }
            
            return $folder . $this->getId() . '.png';
        }
        
        return null;
    }
    
    public function haveImage()
    {
        $image = $this->getImageFileName();
        
        if(!is_null($image) && file_exists(PUBLIC_PATH . $image))
        {
            return true;
        }
        
        if(!is_null($image))
        {
            $image = str_replace('.png', '.jpg', $image);
            
            if(file_exists(PUBLIC_PATH . $image))
            {
                return true;
            }
        }
        
        return false;
    }
    
    public function getImage()
    {
        $image = $this->getImageFileName();
        
        if(!is_null($image) && file_exists(PUBLIC_PATH . $image))
        {
            return $image;
        }
        
        if(!is_null($image))
        {
            $image = str_replace('.png', '.jpg', $image);
            
            if(file_exists(PUBLIC_PATH . $image))
            {
                return $image;
            }
        }
        
        return '/img/users/default.png';
    }
}