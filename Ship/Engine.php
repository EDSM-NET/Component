<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Ship;

trait Engine
{
    public function canThrust($fuel = null, $cargo = 0)
    {
        if(is_null($fuel))
        {
            $fuel = $this->calculateFuelCapacity();
        }

        $thruster = $this->getModule('int_engine_');

        if(!is_null($thruster))
        {
            if($thruster['on'] == 1)
            {
                $maxMass = \Alias\Station\Outfitting\MaxMass::get($thruster['refOutfitting']);

                if(is_null($maxMass))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\MaxMass: ' . \Alias\Station\Outfitting\Type::get($thruster['refOutfitting']) . ' (#' . $thruster['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );
                }
                else
                {
                    if(($this->calculateUnladenMass() + $cargo + $fuel) < $maxMass)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function canBoost($fuel = null, $cargo = 0)
    {
        $canTrust = $this->canThrust($fuel, $cargo);

        if($canTrust === true)
        {
            $boostEnergy = \Alias\Ship\BoostEnergy::get($this->getFdId());

            if(is_null($boostEnergy))
            {
                \EDSM_Api_Logger_Alias::log(
                    '\Alias\Ship\BoostEnergy: ' . $this->getName(),
                    array('file' => __FILE__, 'line' => __LINE__,)
                );
            }
            else
            {
                $powerDistributor = $this->getModule(array('int_powerdistributor_', 'int_guardianpowerdistributor_'));

                if(!is_null($powerDistributor))
                {
                    $enginesCapacity = \Alias\Station\Outfitting\EnginesCapacity::get($powerDistributor['refOutfitting']);

                    if(is_null($enginesCapacity))
                    {
                        \EDSM_Api_Logger_Alias::log(
                            '\Alias\Station\Outfitting\EnginesCapacity: ' . \Alias\Station\Outfitting\Type::get($powerDistributor['refOutfitting']) . ' (#' . $powerDistributor['refOutfitting'] . ')',
                            array('file' => __FILE__, 'line' => __LINE__,)
                        );
                    }
                    else
                    {
                        $enginesCapacity = $this->calculateModuleModifiedValue($enginesCapacity, $powerDistributor, 'EnginesCapacity');

                        if($enginesCapacity > $boostEnergy)
                        {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function calculateSpeed($enginePips, $fuel = null, $cargo = 0, $boost = false)
    {
        if(is_null($fuel))
        {
            $fuel = $this->calculateFuelCapacity();
        }

        $thruster = $this->getModule('int_engine_');
        if(!is_null($thruster))
        {
            if($thruster['on'] == 1)
            {
                $currentMass        = round($this->calculateUnladenMass()) + $fuel + $cargo;
                $engineOptimalMass  = $this->getEngineOptimalMass($thruster);

                if(is_null($engineOptimalMass))
                {
                    return 0;
                }

                $minMass        = $this->getEngineMinMass($thruster);
                $maxMass        = $this->getEngineMaxMass($thruster);

                if(is_null($minMass))
                {
                    return 0;
                }
                if(is_null($maxMass))
                {
                    return 0;
                }

                $optMul         = $this->getEngineOptimalMultiplier($thruster);

                if(is_null($optMul))
                {
                    return 0;
                }

                $optMulBase     = \Alias\Station\Outfitting\OptMultiplierSpeed::get($thruster['refOutfitting']);
                $minMul         = \Alias\Station\Outfitting\MinMultiplierSpeed::get($thruster['refOutfitting']);
                $maxMul         = \Alias\Station\Outfitting\MaxMultiplierSpeed::get($thruster['refOutfitting']);

                if(is_null($optMulBase))
                {
                    return 0;
                }

                if(is_null($minMul))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\MinMultiplierSpeed: ' . \Alias\Station\Outfitting\Type::get($thruster['refOutfitting']) . ' (#' . $thruster['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }
                if(is_null($maxMul))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\MaxMultiplierSpeed: ' . \Alias\Station\Outfitting\Type::get($thruster['refOutfitting']) . ' (#' . $thruster['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $multiplier     = $optMul / $optMulBase;
                $minMul         = $minMul * $multiplier;
                $maxMul         = $maxMul * $multiplier;
                //$minMul         = round(($minMul * $multiplier), 4);
                //$maxMul         = round(($maxMul * $multiplier), 4);

                $topSpeed   = \Alias\Ship\TopSpeed::get($this->getFdId());
                $baseSpeed  = \Alias\Ship\BaseSpeed::get($this->getFdId());

                if(is_null($topSpeed))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Ship\TopSpeed: ' . $this->getName() . ' (#' . $this->getFdId() . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }
                if(is_null($baseSpeed))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Ship\BaseSpeed: ' . $this->getName() . ' (#' . $this->getFdId() . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $result =  $this->_calculateValue(
                    $currentMass,
                    $minMass,
                    $engineOptimalMass,
                    $maxMass,
                    $minMul,
                    $optMul,
                    $maxMul,
                    $topSpeed,
                    $baseSpeed,
                    $enginePips
                );

                if($boost === true)
                {
                    $boostSpeed     = \Alias\Ship\BoostSpeed::get($this->getFdId());
                    $result        *= ($boostSpeed / $topSpeed);
                }

                return $result;
            }
        }


        return 0;
    }

    public function calculateTopSpeed()
    {
        return $this->calculateSpeed(4);
    }

    public function calculateTopBoost()
    {
        return $this->calculateSpeed(4, null, 0, true);
    }


    public function getEngineOptimalMass($module)
    {
        $engineOptimalMass        = $this->calculateModuleModifiedValue(\Alias\Station\Outfitting\EngineOptimalMass::get($module['refOutfitting']), $module, 'EngineOptimalMass');

        if(is_null($engineOptimalMass))
        {
            \EDSM_Api_Logger_Alias::log(
                '\Alias\Station\Outfitting\EngineOptimalMass: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                array('file' => __FILE__, 'line' => __LINE__,)
            );

            return null;
        }

        return $engineOptimalMass;
    }

    public function getEngineMinMass($module)
    {
        $minMass    = \Alias\Station\Outfitting\MinMass::get($module['refOutfitting']);

        if(is_null($minMass))
        {
            \EDSM_Api_Logger_Alias::log(
                '\Alias\Station\Outfitting\MinMass: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                array('file' => __FILE__, 'line' => __LINE__,)
            );

            return null;
        }

        $engineOptimalMass    = $this->getEngineOptimalMass($module);

        if(!is_null($engineOptimalMass))
        {
            $minMass    = round(
                ($minMass * ($engineOptimalMass / \Alias\Station\Outfitting\EngineOptimalMass::get($module['refOutfitting']))),
                2
            );
        }

        return $minMass;
    }

    public function getEngineMaxMass($module)
    {
        $maxMass    = \Alias\Station\Outfitting\MaxMass::get($module['refOutfitting']);

        if(is_null($maxMass))
        {
            \EDSM_Api_Logger_Alias::log(
                '\Alias\Station\Outfitting\MaxMass: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                array('file' => __FILE__, 'line' => __LINE__,)
            );

            return null;
        }

        $engineOptimalMass    = $this->getEngineOptimalMass($module);

        if(!is_null($engineOptimalMass))
        {
            $maxMass    = round(
                ($maxMass * ($engineOptimalMass / \Alias\Station\Outfitting\EngineOptimalMass::get($module['refOutfitting']))),
                2
            );
        }

        return $maxMass;
    }

    public function getEngineOptimalMultiplier($module)
    {
        $optMul         = \Alias\Station\Outfitting\OptMultiplierSpeed::get($module['refOutfitting']);

        if(is_null($optMul))
        {
            \EDSM_Api_Logger_Alias::log(
                '\Alias\Station\Outfitting\OptMultiplierSpeed: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                array('file' => __FILE__, 'line' => __LINE__,)
            );

            return 0;
        }

        $optMul         = $this->calculateModuleModifiedValue($optMul, $module, 'EngineOptPerformance');

        return $optMul;
    }
}