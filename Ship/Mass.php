<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Ship;

trait Mass
{
    public function calculateHullMass()
    {
        $hullMass = \Alias\Ship\HullMass::get($this->getFdId());

        if(!is_null($hullMass))
        {
            return $hullMass;
        }

        return 0;
    }

    public function calculateUnladenMass()
    {
        $mass  = $this->calculateHullMass();
        $mass += $this->calculateModulesMass();

        return $mass;
    }

    public function calculateLadenMass()
    {
        $mass  = $this->calculateUnladenMass();
        $mass += $this->calculateCargoCapacity();
        $mass += $this->calculateFuelCapacity();

        return $mass;
    }

    public function calculateModulesMass()
    {
        $modules    = $this->getModules();

        if(!is_null($modules))
        {
            $mass = 0;

            foreach($modules AS $module)
            {
                if(!is_null($module['refOutfitting']) && \Alias\Station\Outfitting\Mass::isIndex($module['refOutfitting']))
                {
                    $mass += $this->calculateModuleModifiedValue(\Alias\Station\Outfitting\Mass::get($module['refOutfitting']), $module, 'Mass');
                }
                else
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\Mass: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );
                }
            }

            return $mass;
        }

        return 0;
    }
}