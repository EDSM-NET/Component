<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait EDTS
{
    // git clone https://bitbucket.org/Esvandiary/edts.git

    public function getFromEDTS($onlyFromCache = false)
    {
        $cache      = self::getCache('database');
        $cacheKey   = 'EDSM_System_getFromEDTS_' . $this->getId();
        //$cache->remove($cacheKey);
        $result     = $cache->load($cacheKey);

        if($result === false)
        {
            if($onlyFromCache === true)
            {
                return null;
            }

            exec('python3 ' . LIBRARY_PATH . '/Component/System/EDTS/coordsEDSM.py "' . $this->getName() . '"', $edts);

            //Zend_Debug::dump($edts);

            if(array_key_exists(0, $edts) && $edts[0] == $this->getName() && (float) ($edts[2] * sqrt(3)) <= 50)
            {
                $coordinates    = \Zend_Json::decode($edts[1]);
                $coordinates[4] = round((float) ($edts[2] * sqrt(3)));

                $result         =  $coordinates;
            }
            else
            {
                $result         = null;
            }

            $cache->save($result, $cacheKey, array(), rand(86400, 86400 * 31)); // Between 1 day and 1 month
        }

        return $result;
    }

    public function getId64FromEDTS()
    {

        if(!is_null($this->getX()))
        {
            exec('python3 ' . LIBRARY_PATH . '/Component/System/EDTS/id64EDSM.py "' . $this->getName() . '" "' . $this->getX() / 32 . '" "' . $this->getY() / 32 . '" "' . $this->getZ() / 32 . '"', $edts);
        }
        else
        {
            exec('python3 ' . LIBRARY_PATH . '/Component/System/EDTS/id64EDSM.py "' . $this->getName() . '" NULL NULL NULL', $edts);
        }

        //Zend_Debug::dump($edts);

        if(array_key_exists(0, $edts) && $edts[0] == $this->getName())
        {
            if(array_key_exists(1, $edts) && !empty($edts[1]))
            {
                return (int) $edts[1];
            }
        }

        return null;
    }
}