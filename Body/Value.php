<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\Body;

trait Value
{
    public function getEstimatedValue($applyMappingMultiplier = false, $isFirstDiscoverer = false, $isFirstMapper = false)
    {
        // Call a static method which can be used without instancing the complete body
        return static::calculateEstimatedValue(
            $this->getMainType(),
            $this->getType(),
            $this->getMass(),
            $this->getTerraformState(),
            array(
                'haveMapped'            => $applyMappingMultiplier,

                'isFirstDiscoverer'     => $isFirstDiscoverer,
                'isFirstMapper'         => $isFirstMapper,
            )
        );
    }

    /*
     *  @SEE https://forums.frontier.co.uk/showthread.php/232000-Exploration-value-formulae/
     */
    //TODO: Main star value = Normal Main Star Calculation + SUM(MAX(Planetary Body FSS Value / 3.0, 500)) + SUM(Stellar Body FSS Value / 3.0)
    //TODO: There is a bonus of 1k per body for fully FSSing the system (so 8k), and a bonus of 10k per mapable body for fully mapping the system (so 70k).
    static public function calculateEstimatedValue($mainType, $type, $mass, $terraformState, $options)
    {
        // Merge default options
        $options = array_merge(array(
            'dateScanned'           => null,

            'isFirstDiscoverer'     => false,
            'isFirstMapper'         => false,

            'haveMapped'            => false,
            'efficiencyBonus'       => false,

            'systemBodies'          => array(),
            'isPrimaryStar'         => false,
        ), $options);


        if(!is_null($options['dateScanned']))
        {
            if(strtotime($options['dateScanned']) < strtotime('2017-04-11 12:00:00'))
            {
                return static::calculateEstimatedValueFrom22(
                    $mainType,
                    $type,
                    $terraformState
                );
            }

            if(strtotime($options['dateScanned']) < strtotime('2018-12-11 12:00:00'))
            {
                return static::calculateEstimatedValueFrom32(
                    $mainType,
                    $type,
                    $mass,
                    $terraformState
                );
            }
        }

        $value  = 0;
        $bonus  = 0;

        if(is_null($mass))
        {
            $mass = 1;
        }

        if($mainType == 'Star' || $mainType == 1)
        {
            $value = 1200;

            // White Dwarf Star
            if(in_array($type, array(51, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514)))
            {
                $value = 14057;
            }

            // Neutron Star, Black Hole
            if(in_array($type, array(91, 92)))
            {
                $value = 22628;
            }

            // Supermassive Black Hole
            if(in_array($type, array(93)))
            {
                // this is applying the same scaling to the 3.2 value as a normal black hole, not confirmed in game
                $value = 33.5678;
            }

            return round($value + ($mass * $value / 66.25));
        }

        if($mainType == 'Planet' || $mainType == 2)
        {
            $value = 300;

            if(!is_null($terraformState) && $terraformState > 0)
            {
                $bonus = 93328;
            }

            // Metal-rich body
            if(in_array($type, array(1)))
            {
                $value = 21790;
                $bonus = 0;

                if(!is_null($terraformState) && $terraformState > 0)
                {
                    $bonus = 65631;
                }
            }

            // Ammonia world
            if(in_array($type, array(51)))
            {
                $value = 96932;
                $bonus = 0;
            }

            // Class I gas giant
            if(in_array($type, array(71)))
            {
                $value = 1656;
                $bonus = 0;
            }

            // High metal content world / Class II gas giant
            if(in_array($type, array(2, 72)))
            {
                $value = 9654;
                $bonus = 0;

                if(!is_null($terraformState) && $terraformState > 0)
                {
                    $bonus = 100677;
                }
            }

            // Water world
            if(in_array($type, array(41)))
            {
                $value = 64831;
                $bonus = 0;

                if(!is_null($terraformState) && $terraformState > 0)
                {
                    $bonus = 116295;
                }
            }

            // Earth-like world
            if(in_array($type, array(31)))
            {
                $value = 116295;
                $bonus = 0;
            }

            // CALCULATION
            $q              = 0.56591828;
            $value          = $value + $bonus;
            $mapMultiplier  = 1;

            if($options['haveMapped'] === true)
            {
                $mapMultiplier = 3.3333333333;

                if($options['isFirstDiscoverer'] === true && $options['isFirstMapper'] === true)
                {
                    $mapMultiplier = 3.699622554;
                }
                elseif($options['isFirstDiscoverer'] === false && $options['isFirstMapper'] === true)
                {
                    $mapMultiplier = 8.0956;
                }

                if($options['efficiencyBonus'] === true)
                {
                    $mapMultiplier *= 1.25;
                }
            }

            $value = max(($value + ($value * pow($mass, 0.2) * $q)) * $mapMultiplier, 500);

            if($options['isFirstDiscoverer'] === true)
            {
                $value *= 2.6;
            }

            return round($value);
        }

        return 0;
    }

    static public function calculateEstimatedValueFrom32($mainType, $type, $mass, $terraformState)
    {
        $value  = 0;
        $bonus  = 0;

        if(is_null($mass))
        {
            $mass = 1;
        }

        if($mainType == 'Star' || $mainType == 1)
        {
            $value = 2880;

            // White Dwarf Star
            if(in_array($type, array(51, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514)))
            {
                $value = 33737;
            }

            // Neutron Star, Black Hole
            if(in_array($type, array(91, 92)))
            {
                $value = 54309;
            }

            // Supermassive Black Hole
            if(in_array($type, array(93)))
            {
                $value = 80.5654;
            }

            return round($value + ($mass * $value / 66.25));
        }

        if($mainType == 'Planet' || $mainType == 2)
        {
            $value = 720;

            if(!is_null($terraformState) && $terraformState > 0)
            {
                $bonus = 223971;
            }

            // Metal-rich body
            if(in_array($type, array(1)))
            {
                $value = 52292;

                if(!is_null($terraformState) && $terraformState > 0)
                {
                    $bonus = 245306;
                }
                else
                {
                    $bonus = 0;
                }
            }

            // High metal content world / Class II gas giant
            if(in_array($type, array(2, 72)))
            {
                $value = 23168;

                if(!is_null($terraformState) && $terraformState > 0)
                {
                    $bonus = 241607;
                }
                else
                {
                    $bonus = 0;
                }
            }

            // Earth-like world
            if(in_array($type, array(31)))
            {
                $value = 155581;
                $bonus = 279088;
            }

            // Water world
            if(in_array($type, array(41)))
            {
                $value = 155581;

                if(!is_null($terraformState) && $terraformState > 0)
                {
                    $bonus = 279088;
                }
                else
                {
                    $bonus = 0;
                }
            }

            // Ammonia world
            if(in_array($type, array(51)))
            {
                $value = 232619;
                $bonus = 0;
            }

            // Class I gas giant
            if(in_array($type, array(71)))
            {
                $value = 3974;
                $bonus = 0;
            }

            $value = $value + (3 * $value * pow($mass, 0.199977) / 5.3);
            $bonus = $bonus + (3 * $bonus * pow($mass, 0.199977) / 5.3);
        }

        return round($value + $bonus);
    }

    static public function calculateEstimatedValueFrom22($mainType, $type, $terraformState)
    {
        if($mainType == 'Star' || $mainType == 1)
        {
            // O (Blue-White) Star
            if(in_array($type, array(1)))
            {
                return 4170;
            }
            // B (Blue-White) Star
            if(in_array($type, array(2)))
            {
                return 3098;
            }
            // A (Blue-White) Star, A (Blue-White super giant) Star
            if(in_array($type, array(3, 301)))
            {
                return 2950;
            }
            // F (White) Star, F (White super giant) Star
            if(in_array($type, array(4, 401)))
            {
                return 2932;
            }
            // G (White-Yellow) Star, G (White-Yellow super giant) Star
            if(in_array($type, array(5, 5001)))
            {
                return 2923;
            }
            // K (Yellow-Orange) Star, K (Yellow-Orange giant) Star
            // M (Red dwarf) Star, M (Red giant) Star, M (Red super giant) Star',
            if(in_array($type, array(6, 601, 7, 701, 702)))
            {
                return 2911;
            }
            // L (Brown dwarf) Star
            if(in_array($type, array(8)))
            {
                return 2887;
            }
            // T (Brown dwarf) Star
            if(in_array($type, array(9)))
            {
                return 2883;
            }
            // Y (Brown dwarf) Star
            if(in_array($type, array(10)))
            {
                return 2881;
            }
            // T Tauri Star
            if(in_array($type, array(11)))
            {
                return 2900;
            }
            // Herbig Ae/Be Star
            if(in_array($type, array(12)))
            {
                return 2500;
            }
            // Wolf-Rayet Star, Wolf-Rayet N Star, Wolf-Rayet NC Star, Wolf-Rayet C Star, Wolf-Rayet O Star
            if(in_array($type, array(21, 22, 23, 24, 25)))
            {
                return 7794;
            }
            // CS Star, C Star, CN Star, CJ Star, CH Star, CHd Star
            if(in_array($type, array(31, 32, 33, 34, 35, 36)))
            {
                return 2920;
            }
            // White Dwarf Star
            if(in_array($type, array(51, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514)))
            {
                return 26000;
            }
            // Neutron Star
            if(in_array($type, array(91)))
            {
                return 43441;
            }
            // Black Hole, Supermassive Black Hole
            if(in_array($type, array(92, 93)))
            {
                return 61439;
            }

            return 2000;
        }

        if($mainType == 'Planet' || $mainType == 2)
        {
            // Metal-rich body
            if(in_array($type, array(1)))
            {
                return 12449; // 0.51 EM
            }

            // High metal content world
            if(in_array($type, array(2)))
            {
                if(!is_null($terraformState) && $terraformState > 0)
                {
                    return 42000;
                }
                else
                {
                    return 6670; // 0.41
                }
            }

            // Rocky body
            if(in_array($type, array(11)))
            {
                if(!is_null($terraformState) && $terraformState > 0)
                {
                    return 37000;
                }
                else
                {
                    return 933; // 0.04
                }
            }

            // Rocky Ice world
            if(in_array($type, array(12)))
            {
                return 933; // 0.04
            }

            // Icy body
            if(in_array($type, array(21)))
            {
                return 933; // 0.04
            }

            // Earth-like world
            if(in_array($type, array(31)))
            {
                return 67798; // 0.47 EM
            }

            // Water world
            if(in_array($type, array(41)))
            {
                return 30492; // (0.82 EM)
            }

            // Ammonia world
            if(in_array($type, array(51)))
            {
                return 40322; // (0.41 EM)
            }

            // Class I gas giant
            if(in_array($type, array(71)))
            {
                return 3400;  // 62.93 EM
            }

            // Class II gas giant
            if(in_array($type, array(72)))
            {
                return 12319;  // 260.84 EM
            }

            // Class III gas giant
            if(in_array($type, array(73)))
            {
                return 2339; // 990.92 EM
            }

            // Class IV gas giant
            if(in_array($type, array(74)))
            {
                return 2782; // 3319 em
            }

            // Class V gas giant
            if(in_array($type, array(75)))
            {
                return 2225;
            }

            // Water giant, Water giant with life
            // Gas giant with water-based life, Gas giant with ammonia-based life
            // Helium-rich gas giant, Helium gas giant
            if(in_array($type, array(42, 43, 61, 62, 81, 82)))
            {
                return 2000;
            }
        }

        return 0;
    }
}