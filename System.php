<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component;

class System extends Instance
{
    use \Component\System\Body;
    use \Component\System\GalacticCoordinate;
    use \Component\System\Power;
    use \Component\System\Station;
    
    protected $_defaultModel    = 'Models_Systems';
    protected $_primaryKey      = 'id';

    public function getId64()
    {
        if($this->isValid())
        {
            $id64 = $this->getIdentity('id64');

            if(!is_null($id64))
            {
                return (int) $id64;
            }
        }

        return null;
    }

    public function getName()
    {
        if($this->isValid())
        {
            return $this->getIdentity('name');
        }

        return null;
    }

    /**
     * Placeholder for count distance
     */
    public function getCountKnownRefs()
    {
        if($this->isValid())
        {
            $countKnownRefs = $this->getIdentity('countKnownRefs');

            if(!is_null($countKnownRefs))
            {
                return (int) $countKnownRefs;
            }
        }

        return null;
    }

    public function getLastTrilateration()
    {
        if($this->isValid())
        {
            $lastTrilateration = $this->getIdentity('lastTrilateration');

            if(!is_null($lastTrilateration))
            {
                return $lastTrilateration;
            }
        }

        return null;
    }



    /**
     * Coordinates
     */
    public function getX()
    {
        if($this->isValid())
        {
            return $this->getIdentity('x');
        }

        return null;
    }

    public function getY()
    {
        if($this->isValid())
        {
            return $this->getIdentity('y');
        }

        return null;
    }

    public function getZ()
    {
        if($this->isValid())
        {
            return $this->getIdentity('z');
        }

        return null;
    }


    public function isCoordinatesLocked()
    {
        if($this->isValid())
        {
            if($this->getIdentity('coordinatesLocked') == 1)
            {
                return true;
            }
        }

        return false;
    }
}