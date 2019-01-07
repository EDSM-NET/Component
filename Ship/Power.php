<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Ship;

trait Power
{
    public function getPowerGenerated()
    {
        $modules    = $this->getModules();

        if(!is_null($modules))
        {
            foreach($modules AS $module)
            {
                if($module['refSlot'] == 351)
                {
                    if(!is_null($module['refOutfitting']) && \Alias\Station\Outfitting\PowerCapacity::isIndex($module['refOutfitting']))
                    {
                        $originalPower = \Alias\Station\Outfitting\PowerCapacity::get($module['refOutfitting']);
                        $modifiedPower = $this->calculateModuleModifiedValue($originalPower, $module, 'PowerCapacity');

                        return $modifiedPower;
                    }
                    else
                    {
                        \EDSM_Api_Logger_Alias::log(
                            '\Alias\Station\Outfitting\PowerCapacity: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                            array('file' => __FILE__, 'line' => __LINE__,)
                        );
                    }
                }
            }
        }

        return 0;
    }

    public function getPowerUsed()
    {
        $modules    = $this->getModules();

        if(!is_null($modules))
        {
            $powerUsed = 0;

            foreach($modules AS $module)
            {
                if(!in_array($module['refSlot'], [301, 351]))
                {
                    if(!is_null($module['refOutfitting']) && \Alias\Station\Outfitting\PowerDraw::isIndex($module['refOutfitting']))
                    {
                        $originalPower = \Alias\Station\Outfitting\PowerDraw::get($module['refOutfitting']);
                        $modifiedPower = $this->calculateModuleModifiedValue($originalPower, $module, 'PowerCapacity');

                        $powerUsed    += $modifiedPower;
                    }
                    else
                    {
                        \EDSM_Api_Logger_Alias::log(
                            '\Alias\Station\Outfitting\Power: ' . \Alias\Station\Outfitting\Type::get($module['refOutfitting']) . ' (#' . $module['refOutfitting'] . ')',
                            array('file' => __FILE__, 'line' => __LINE__,)
                        );
                    }
                }
            }

            return $powerUsed;
        }

        return 0;
    }
}