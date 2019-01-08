<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Ship;

trait Cargo
{
    public function calculateCargoCapacity()
    {
        $capacity   = 0;
        $modules    = $this->getModules();

        if(!is_null($modules))
        {
            foreach($modules AS $module)
            {
                if((stripos(\Alias\Station\Outfitting\Type::getToFd($module['refOutfitting']), 'int_cargorack_') !== false || stripos(\Alias\Station\Outfitting\Type::getToFd($module['refOutfitting']), 'int_corrosionproofcargorack_') !== false) && $module['on'] == 1)
                {
                    if(!is_null($module['refOutfitting']) && \Alias\Station\Outfitting\Capacity::isIndex($module['refOutfitting']))
                    {
                        $capacity += (int) \Alias\Station\Outfitting\Capacity::get($module['refOutfitting']);
                    }
                }
            }
        }

        return $capacity;
    }
}