<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait GalacticMapping
{
    protected $_galacticMappingEntries = false;

    public function getGalacticMappingEntries()
    {
        if($this->_galacticMappingEntries === false)
        {
            $this->_galacticMappingEntries = self::getModel('Models_Galactic_Mapping')->getByRefSystem($this->getId());
        }

        return $this->_galacticMappingEntries;
    }

    public function getCommunityName()
    {
        if($this->isValid())
        {
            $galacticMappingEntries = $this->getGalacticMappingEntries();

            if(!is_null($galacticMappingEntries) && is_array($galacticMappingEntries))
            {
                $communityNames = array();
                foreach($galacticMappingEntries AS $galacticMappingEntry)
                {
                    if(array_key_exists('name', $galacticMappingEntry) && !is_null($galacticMappingEntry['name']))
                    {
                        if($galacticMappingEntry['name'] != $this->getName())
                        {
                            $communityNames[] = $galacticMappingEntry['name'];
                        }
                    }
                }

                if(count($communityNames) > 0)
                {
                    return implode(' / ', $communityNames);
                }
            }
        }

        return null;
    }

    public function isPOI()
    {
        if($this->isValid())
        {
            $galacticMappingEntries = $this->getGalacticMappingEntries();

            if(!is_null($galacticMappingEntries))
            {
                foreach($galacticMappingEntries AS $galacticMappingEntry)
                {
                    if($galacticMappingEntry['isPOI'] == 1)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}