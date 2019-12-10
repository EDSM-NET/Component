<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Ship;

trait Armour
{
    public function calculateArmourIntegrity()
    {
        $integrity          = 0;
        $baseArmour         = \Alias\Ship\Armour::get($this->getFdId());

        $modules            = $this->getModules();
        $bulkheadModule     = null;

        if(!is_null($baseArmour))
        {
            $integrity+= $baseArmour;
        }
        else
        {
            \EDSM_Api_Logger_Alias::log(
                '\Alias\Ship\Armour: ' . \Alias\Ship\Type::get($this->getFdId()) . ' (#' . $this->getFdId() . ')',
                array('file' => __FILE__, 'line' => __LINE__,)
            );
        }

        if(!is_null($modules))
        {
            foreach($modules AS $module)
            {
                // Find bulkhead
                if($module['refSlot'] == 301)
                {
                    $bulkheadModule = $module;
                }
                elseif(
                       ($module['refOutfitting'] > 4800 && $module['refOutfitting'] < 4900) // Add Hull Reinforcement Package
                    || ($module['refOutfitting'] > 6000 && $module['refOutfitting'] < 6100) // Add Meta Alloy Hull Reinforcement
                    || ($module['refOutfitting'] > 6100 && $module['refOutfitting'] < 6200) // Add Guardian Hull Reinforcement
                )
                {
                    $defenceModifierHealthAddition = \Alias\Station\Outfitting\DefenceModifierHealthAddition::get($module['refOutfitting']);

                    if(!is_null($defenceModifierHealthAddition))
                    {
                        $defenceModifierHealthAddition = $this->calculateModuleModifiedValue($defenceModifierHealthAddition, $module, 'DefenceModifierHealthAddition');
                        $integrity                    += $defenceModifierHealthAddition;
                    }
                    else
                    {
                        \EDSM_Api_Logger_Alias::log(
                            '\Alias\Station\Outfitting\DefenceModifierHealthAddition: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                            array('file' => __FILE__, 'line' => __LINE__,)
                        );
                    }

                    $defenceModifierHealthMultiplier = \Alias\Station\Outfitting\DefenceModifierHealthMultiplier::get($module['refOutfitting']);

                    if(!is_null($defenceModifierHealthMultiplier))
                    {
                        $defenceModifierHealthMultiplier = $this->calculateModuleModifiedValue($defenceModifierHealthMultiplier, $module, 'DefenceModifierHealthMultiplier');
                        $integrity                      += $baseArmour * $defenceModifierHealthMultiplier / 100;
                    }
                    else
                    {
                        \EDSM_Api_Logger_Alias::log(
                            '\Alias\Station\Outfitting\DefenceModifierHealthMultiplier: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                            array('file' => __FILE__, 'line' => __LINE__,)
                        );
                    }
                }
            }
        }

        // Add hull boost from bulkhead
        if(!is_null($bulkheadModule))
        {
            $defenceModifierHealthMultiplier = \Alias\Station\Outfitting\DefenceModifierHealthMultiplier::get($bulkheadModule['refOutfitting']);

            if(!is_null($defenceModifierHealthMultiplier))
            {
                $defenceModifierHealthMultiplier = $this->calculateModuleModifiedValue($defenceModifierHealthMultiplier, $bulkheadModule, 'DefenceModifierHealthMultiplier');
                $integrity                      += $baseArmour * $defenceModifierHealthMultiplier / 100;
            }
            else
            {
                \EDSM_Api_Logger_Alias::log(
                    '\Alias\Station\Outfitting\DefenceModifierHealthMultiplier: ' . \Alias\Station\Outfitting\Type::get($bulkheadModule['refOutfitting']) . ' (#' . $bulkheadModule['refOutfitting'] . ')',
                    array('file' => __FILE__, 'line' => __LINE__,)
                );
            }
        }

        return round($integrity);
    }

    public function calculateArmourResistance($resistanceType)
    {
        $resistance         = 1;
        $resistanceType     = ucfirst($resistanceType) . 'Resistance';

        $modules            = $this->getModules();
        $bulkheadModule     = null;
        $useClass           = 'Alias\Station\Outfitting\\' . $resistanceType;

        if(file_exists(LIBRARY_PATH . '/' . str_replace(['\\', '_'], ['/', '/'], $useClass) . '.php'))
        {
            if(!is_null($modules))
            {
                foreach($modules AS $module)
                {
                    // Find bulkhead
                    if($module['refSlot'] == 301)
                    {
                        $bulkheadModule = $module;
                    }
                    elseif(
                           ($module['refOutfitting'] > 4800 && $module['refOutfitting'] < 4900) // Add Hull Reinforcement Package
                        || ($module['refOutfitting'] > 6000 && $module['refOutfitting'] < 6100) // Add Meta Alloy Hull Reinforcement
                        || ($module['refOutfitting'] > 6100 && $module['refOutfitting'] < 6200) // Add Guardian Hull Reinforcement
                    )
                    {
                        $currentResistance = $useClass::get($module['refOutfitting']);

                        if(!is_null($currentResistance))
                        {
                            $currentResistance  = $this->calculateModuleModifiedValue($currentResistance, $module, $resistanceType);
                            $currentResistance /= 100;

                            $resistance        *= 1 - $currentResistance;
                        }
                        else
                        {
                            \EDSM_Api_Logger_Alias::log(
                                '\Alias\Station\Outfitting\\' . $resistanceType . ': ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                                array('file' => __FILE__, 'line' => __LINE__,)
                            );
                        }
                    }
                }
            }
        }

        // Add resistance from bulkhead
        if(!is_null($bulkheadModule))
        {
            $bulkheadResistance = $useClass::get($bulkheadModule['refOutfitting']);

            if(!is_null($bulkheadResistance))
            {
                $bulkheadResistance  = $this->calculateModuleModifiedValue($bulkheadResistance, $bulkheadModule, $resistanceType);
                $bulkheadResistance /= 100;

                $resistance          = (1 - $bulkheadResistance) * $resistance;
                $resistance          = $this->_diminishArmourMultiplier(0.7, $resistance);
            }
        }

        return (1 - $resistance) * 100;
    }

    private function _diminishArmourMultiplier($diminishFrom, $damageMult)
    {
        if($damageMult > $diminishFrom)
        {
            return $damageMult;
        }
        else
        {
            return ($diminishFrom / 2) + 0.5 * $damageMult;
        }
    }
}