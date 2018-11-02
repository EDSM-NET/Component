<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component;

class System extends Instance
{
    use \Component\System\Body;
    use \Component\System\Death;
    use \Component\System\EDTS;
    use \Component\System\GalacticCoordinate;
    use \Component\System\GalacticMapping;
    use \Component\System\Galnet;
    use \Component\System\Information;
    use \Component\System\Permit;
    use \Component\System\Power;
    use \Component\System\Station;
    use \Component\System\Traffic;

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
            return $this->getIdentity('lastTrilateration');
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


    /**
     * Hidden? Merged?
     */
    public function isHidden()
    {
        if($this->isValid())
        {
            $isHidden = $this->getHide();

            if(is_null($isHidden))
            {
                return false;
            }
        }

        return true;
    }

    protected $_hide = false;
    public function getHide($field = 'hiddenAt')
    {
        if($this->isValid())
        {
            if($this->_hide === false)
            {
                $this->_hide = self::getModel('Models_Systems_Hides')->getByRefSystem($this->getId());
            }

            if(!is_null($this->_hide) && array_key_exists($field, $this->_hide))
            {
                return $this->_hide[$field];
            }
        }

        return null;
    }

    public function getMergedTo()
    {
        return $this->getHide('mergedTo');
    }


    /**
     * Duplicates?
     */
    protected $_duplicates = false;
    public function getDuplicates()
    {
        if($this->_duplicates === false)
        {
            $this->_duplicates = self::getModel('Models_Systems_Duplicates')->getByRefSystem($this->getId());
        }

        if(!is_null($this->_duplicates) && is_array($this->_duplicates) && count($this->_duplicates) > 0)
        {
            return $this->_duplicates;
        }

        return null;
    }


    /**
     * Featured?
     */
    protected $_featured = false;
    public function getPushedToFront()
    {
        if($this->isValid() && $this->isHidden() === false)
        {
            if($this->_featured === false)
            {
                $this->_featured = self::getModel('Models_Systems_Featured')->getByRefSystem($this->getId());
            }

            if(!is_null($this->_featured) && array_key_exists('featuredAt', $this->_featured))
            {
                return $this->_featured['featuredAt'];
            }
        }

        return null;
    }

    public function isPushedToFront()
    {
        if($this->isValid() && $this->isHidden() === false)
        {
            $featuredStatus = $this->getPushedToFront();

            if(!is_null($featuredStatus))
            {
                return true;
            }
        }

        return false;
    }



    public function getUpdateTime()
    {
        if($this->isValid())
        {
            return $this->getIdentity('updatetime');
        }

        return null;
    }
}