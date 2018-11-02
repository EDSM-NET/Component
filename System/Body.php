<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Body
{
    protected $_bodies          = false;
    protected $_bodiesCount     = false;

    protected $_allMaterials    = false;

    public function getBodies()
    {
        if($this->_bodies === false)
        {
            $this->_bodies = self::getModel('Models_Systems_Bodies')->getByRefSystem($this->getId());
        }

        return $this->_bodies;
    }

    public function getPrimaryStar()
    {
        $bodies = $this->getBodies();

        if(!is_null($bodies) && count($bodies) > 0)
        {
            foreach($bodies AS $body)
            {
                $body = \EDSM_System_Body::getInstance($body['id']);

                if($body->isMainStar() === true)
                {
                    return $body;
                }
            }
        }

        return null;
    }

    public function getCountBodies()
    {
        return count($this->getBodies());
    }

    public function getGameBodiesCount()
    {
        if($this->_bodiesCount === false)
        {
            $this->_bodiesCount = self::getModel('Models_Systems_Bodies_Count')->getByRefSystem($this->getId());
        }

        return $this->_bodiesCount;
    }



    public function getAllMaterials()
    {
        if($this->_allMaterials === false)
        {
            $systemMaterials    = array();
            $bodies             = $this->getBodies();

            if(!is_null($bodies) && count($bodies) > 0)
            {
                foreach($bodies AS $body)
                {
                    $body       = \EDSM_System_Body::getInstance($body['id']);
                    $materials  = $body->getMaterials();

                    if(!is_null($materials) && count($materials) > 0)
                    {
                        foreach($materials AS $materialName => $materialValues)
                        {
                            if(!in_array($materialName, $systemMaterials))
                            {
                                $systemMaterials[] = $materialName;
                            }
                        }
                    }
                }
            }

            $this->_allMaterials = $systemMaterials;
        }

        return $this->_allMaterials;
    }

    public function isGreen($force = false)
    {
        if($this->isValid())
        {
            // We already know the result
            if(!is_null($this->getIdentity('isGreen')) && $force === false)
            {
                if($this->getIdentity('isGreen') == 1)
                {
                    return true;
                }

                return false;
            }

            // We need to calculate and store again
            $isGreen = $this->isGreenCalculation();

            self::getModel('Models_Systems')->updateById(
                $this->getId(),
                array('isGreen' => ( ($isGreen === true) ? 1 : 0 )),
                false
            );

            return $isGreen;
        }

        return false;
    }

    public function isGreenCalculation()
    {
        $systemMaterials = $this->getAllMaterials();

        if(count($systemMaterials) > 0)
        {
            if(
                   in_array('Germanium', $systemMaterials)
                && in_array('Carbon', $systemMaterials)
                && in_array('Vanadium', $systemMaterials)
                && in_array('Cadmium', $systemMaterials)
                && in_array('Arsenic', $systemMaterials)
                && in_array('Niobium', $systemMaterials)
                && in_array('Yttrium', $systemMaterials)
                && in_array('Polonium', $systemMaterials)
            )
            {
                return true;
            }
        }

        return false;
    }
}