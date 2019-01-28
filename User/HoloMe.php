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
            return $image . '?v=' . filemtime(PUBLIC_PATH . $image);
        }

        if(!is_null($image))
        {
            $image = str_replace('.png', '.jpg', $image);

            if(file_exists(PUBLIC_PATH . $image))
            {
                return $image . '?v=' . filemtime(PUBLIC_PATH . $image);
            }
        }

        return '/img/users/default.png' . '?v=' . filemtime(PUBLIC_PATH . '/img/users/default.png');
    }

    public function getHashedImage($currentOptions = array(), $realName = false)
    {
        $availableOptionsByPriority = array_keys(\Alias\Commander\Avatar\Category::getAll());
        $lastValue                  = null;

        while(count($availableOptionsByPriority) > 0)
        {
            $testedHash     = array();
            $defaultValue   = null;

            foreach($availableOptionsByPriority AS $availableOption)
            {
                if(array_key_exists($availableOption, $currentOptions))
                {
                    $testedHash[$availableOption] = $currentOptions[$availableOption];
                }
                else
                {
                    // Grab default value
                    $useClass       = '\Alias\Commander\Avatar\\' . $availableOption;
                    $defaultValue   = $useClass::getDefault();

                    if(!is_null($defaultValue))
                    {
                        $testedHash[$availableOption] = $defaultValue;
                    }
                }
            }

            if(count($testedHash) > 0)
            {
                //\Zend_Debug::dump($testedHash);

                $testedHash = md5(implode(';', $testedHash));
                $testFile   = implode('/', array_slice(str_split($testedHash, 1), 0, 5)) . '/' . $testedHash . '.jpg';

                if(file_exists(PUBLIC_PATH . '/img/avatar/' . $testFile) || $realName === true)
                {
                    return '/img/avatar/' . $testFile;
                }
                else
                {
                    // Reverse and add default value and test each time!
                }
            }

            $lastValue      = array_pop($availableOptionsByPriority);
        }

        return null;
    }
}