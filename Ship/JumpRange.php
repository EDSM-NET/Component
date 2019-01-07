<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Ship;

trait JumpRange
{
    public function calculateJumpRange($mass, $fuel = null, $cargo = 0)
    {
        if(is_null($fuel))
        {
            $fuel = $this->calculateFuelCapacity();
        }

        $mass   = $mass + $cargo;
        $fsd    = $this->getModule('int_hyperdrive_');

        if(!is_null($fsd))
        {
            if($fsd['on'] == 1)
            {
                $FSDOptimalMass = $this->calculateModuleModifiedValue(\Alias\Station\Outfitting\FSDOptimalMass::get($fsd['refOutfitting']), $fsd, 'FSDOptimalMass');

                if(is_null($FSDOptimalMass))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\FSDOptimalMass: ' . \Alias\Station\Outfitting\Type::get($fsd['refOutfitting']) . ' (#' . $fsd['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $maxFuelPerJump = $this->calculateModuleModifiedValue(\Alias\Station\Outfitting\MaxFuelPerJump::get($fsd['refOutfitting']), $fsd, 'MaxFuelPerJump');

                if(is_null($maxFuelPerJump))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\MaxFuelPerJump: ' . \Alias\Station\Outfitting\Type::get($fsd['refOutfitting']) . ' (#' . $fsd['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $fuelMultiplier = \Alias\Station\Outfitting\FuelMultiplier::get($fsd['refOutfitting']);

                if(is_null($fuelMultiplier))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\FuelMultiplier: ' . \Alias\Station\Outfitting\Type::get($fsd['refOutfitting']) . ' (#' . $fsd['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $fuelPower = \Alias\Station\Outfitting\FuelPower::get($fsd['refOutfitting']);

                if(is_null($fuelPower))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\FuelPower: ' . \Alias\Station\Outfitting\Type::get($fsd['refOutfitting']) . ' (#' . $fsd['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $jumpRange = pow(
                    min($fuel, $maxFuelPerJump) / ($fuelMultiplier * 0.001),
                    1 / $fuelPower
                ) * $FSDOptimalMass / $mass;

                // FSD Booster?
                $fsdBooster = $this->getModule('int_guardianfsdbooster_');

                if(!is_null($fsdBooster))
                {
                    $jumpBoost = \Alias\Station\Outfitting\JumpBoost::get($fsdBooster['refOutfitting']);

                    if(!is_null($jumpBoost))
                    {
                        $jumpRange += $jumpBoost;
                    }
                    else
                    {
                        \EDSM_Api_Logger_Alias::log(
                            '\Alias\Station\Outfitting\JumpBoost: ' . \Alias\Station\Outfitting\Type::get($fsdBooster['refOutfitting']) . ' (#' . $fsdBooster['refOutfitting'] . ')',
                            array('file' => __FILE__, 'line' => __LINE__,)
                        );
                    }
                }

                return $jumpRange;
            }
        }

        return 0;
    }

    public function calculateTotalJumpRange($mass, $fuel = null, $cargo = 0)
    {
        if(is_null($fuel))
        {
            $fuel = $this->calculateFuelCapacity();
        }

        $mass           = $mass + $cargo;
        $totalRange     = 0;
        $fuelRemaining  = $fuel;
        $fsd    = $this->getModule('int_hyperdrive_');

        if(!is_null($fsd))
        {
            if($fsd['on'] == 1)
            {
                $maxFuelPerJump = $this->calculateModuleModifiedValue(\Alias\Station\Outfitting\MaxFuelPerJump::get($fsd['refOutfitting']), $fsd, 'MaxFuelPerJump');

                if(is_null($maxFuelPerJump))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\MaxFuelPerJump: ' . \Alias\Station\Outfitting\Type::get($fsd['refOutfitting']) . ' (#' . $fsd['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                 while($fuelRemaining > 0)
                 {
                    $fuelForThisJump = min($fuelRemaining, $maxFuelPerJump);
                    $totalRange     += $this->calculateJumpRange($mass, $fuelForThisJump);

                    $mass           -= $fuelForThisJump;
                    $fuelRemaining  -= $fuelForThisJump;
                 }
            }
        }

        return $totalRange;
    }

    public function calculateMaxJumpRange()
    {
        $fsd    = $this->getModule('int_hyperdrive_');

        if(!is_null($fsd))
        {
            if($fsd['on'] == 1)
            {
                $maxFuelPerJump = $this->calculateModuleModifiedValue(\Alias\Station\Outfitting\MaxFuelPerJump::get($fsd['refOutfitting']), $fsd, 'MaxFuelPerJump');

                if(is_null($maxFuelPerJump))
                {
                    \EDSM_Api_Logger_Alias::log(
                        '\Alias\Station\Outfitting\MaxFuelPerJump: ' . \Alias\Station\Outfitting\Type::get($fsd['refOutfitting']) . ' (#' . $fsd['refOutfitting'] . ')',
                        array('file' => __FILE__, 'line' => __LINE__,)
                    );

                    return 0;
                }

                $mass  = $this->calculateUnladenMass();
                $mass += $maxFuelPerJump; // Add max fuel per jump mass


                return $this->calculateJumpRange($mass, $maxFuelPerJump);
            }
        }

        return 0;
    }

    public function calculateUnladenJumpRange()
    {
        $mass  = $this->calculateUnladenMass();
        $mass += $this->calculateFuelCapacity();

        return $this->calculateJumpRange($mass);
    }

    public function calculateLadenJumpRange()
    {
        $mass  = $this->calculateUnladenMass();
        $mass += $this->calculateFuelCapacity();

        return $this->calculateJumpRange($mass, null, $this->calculateCargoCapacity());
    }

    public function calculateUnladenTotalJumpRange()
    {
        $mass  = $this->calculateUnladenMass();
        $mass += $this->calculateFuelCapacity();

        return $this->calculateTotalJumpRange($mass);
    }

    public function calculateLadenTotalJumpRange()
    {
        $mass  = $this->calculateUnladenMass();
        $mass += $this->calculateFuelCapacity();

        return $this->calculateTotalJumpRange($mass, null, $this->calculateCargoCapacity());
    }
}