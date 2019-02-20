<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

use         Alias\System\HandAuthoredSector;
use         Alias\System\Sector;

trait ID64
{
    private static $pgSystemRegex   = "#^(?P<sector>[\w\s'.()/-]+) (?P<l1>[A-Za-z])(?P<l2>[A-Za-z])-(?P<l3>[A-Za-z]) (?P<mcode>[A-Za-z])(?:(?P<n1>\d+)-)?(?P<n2>\d+)$#";
    private static $sectorSize      = 1280;
    private static $sectorOffset    = [
        'x'                             => -49985,
        'y'                             => -40985,
        'z'                             => -24105,
    ];

    public function getId64($calculate = false)
    {
        if($this->isValid())
        {
            $id64 = $this->getIdentity('id64');

            if(!is_null($id64))
            {
                return (int) $id64;
            }
        }

        // Try to generate it!
        if($calculate === true)
        {
            return $this->calculateId64();
        }

        return null;
    }

    public function calculateId64()
    {
        $systemFragements = $this->getFragmentsFromName();

        if(!is_null($systemFragements))
        {
            $cubeSide       = $this->getCubeSide($systemFragements['mcode']);
            $systemPosition = $this->getEstimatedCoordinates($cubeSide);

            if(!is_null($systemPosition))
            {
                $massCode       = ord(strtolower($systemFragements['mcode'])) - ord('a');
                $boxPosition    = array(
                    'x' => $systemPosition['x'] - (((($systemPosition['x'] - self::$sectorOffset['x']) % $cubeSide) + $cubeSide) % $cubeSide),
                    'y' => $systemPosition['y'] - (((($systemPosition['y'] - self::$sectorOffset['y']) % $cubeSide) + $cubeSide) % $cubeSide),
                    'z' => $systemPosition['z'] - (((($systemPosition['z'] - self::$sectorOffset['z']) % $cubeSide) + $cubeSide) % $cubeSide),
                );

                $boxPosition['x'] = ($boxPosition['x'] - self::$sectorOffset['x']) / $cubeSide;
                $boxPosition['y'] = ($boxPosition['y'] - self::$sectorOffset['y']) / $cubeSide;
                $boxPosition['z'] = ($boxPosition['z'] - self::$sectorOffset['z']) / $cubeSide;

                $id64           = (0 << 9) + (0 & (2**9-1)); // Insert 000000000 body
                $id64           = ($id64 << (11 + ($massCode * 3))) + ((int) $systemFragements['n2'] & (2**(11 + ($massCode * 3))-1));

                $id64           = ($id64 << (14 - $massCode)) + ((int) $boxPosition['x'] & (2**(14 - $massCode)-1));
                $id64           = ($id64 << (13 - $massCode)) + ((int) $boxPosition['y'] & (2**(13 - $massCode)-1));
                $id64           = ($id64 << (14 - $massCode)) + ((int) $boxPosition['z'] & (2**(14 - $massCode)-1));

                $id64           = ($id64 << 3) + ($massCode & (2**3-1));

                return bindec(sprintf('%064b', $id64));
            }
        }

        return null;
    }

    public function getBinaryId64()
    {
        $id64 = $this->getId64();

        if(!is_null($id64))
        {
            return sprintf('%064b', $id64);
        }
        else
        {
            // Try to generate it!
            return $this->calculateId64();
        }

        return null;
    }



    public function getMassCode()
    {
        $binaryId64 = $this->getBinaryId64();

        if(!is_null($binaryId64))
        {
            return bindec(
                substr($binaryId64, -3)
            );
        }

        return null;
    }

    public function getNBoxLength()
    {
        $massCode = $this->getMassCode();

        if(!is_null($massCode))
        {
            return 7 - $massCode;
        }

        return null;
    }



    public function getN2()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            return bindec(
                substr(
                    $this->getBinaryId64(),
                    9,
                    (32 - 3 * $nBoxLength)
                )
            );
        }
        return null;
    }



    public function getXBoxCoordinates()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            if($nBoxLength > 0)
            {
                return bindec(
                    substr(
                        $this->getBinaryId64(),
                        (48 - 3 * $nBoxLength),
                        $nBoxLength
                    )
                );
            }
            else
            {
                return 0;
            }
        }
        return null;
    }

    public function getYBoxCoordinates()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            if($nBoxLength > 0)
            {
                return bindec(
                    substr(
                        $this->getBinaryId64(),
                        (54 - 2 * $nBoxLength),
                        $nBoxLength
                    )
                );
            }
            else
            {
                return 0;
            }
        }

        return null;
    }

    public function getZBoxCoordinates()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            if($nBoxLength > 0)
            {
                return bindec(
                    substr(
                        $this->getBinaryId64(),
                        (61 - $nBoxLength),
                        $nBoxLength
                    )
                );
            }
            else
            {
                return 0;
            }
        }

        return null;
    }



    public function getXSector()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            return bindec(
                substr(
                    $this->getBinaryId64(),
                    (41 - 3 * $nBoxLength),
                    7
                )
            );
        }

        return null;
    }

    public function getYSector()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            return bindec(
                substr(
                    $this->getBinaryId64(),
                    (48 - 2 * $nBoxLength),
                    6
                )
            );
        }

        return null;
    }

    public function getZSector()
    {
        $nBoxLength = $this->getNBoxLength();

        if(!is_null($nBoxLength))
        {
            return bindec(
                substr(
                    $this->getBinaryId64(),
                    (54 - $nBoxLength),
                    7
                )
            );
        }

        return null;
    }



    public function getFragmentsFromName()
    {
        $systemName = $this->getName();

        if(preg_match(self::$pgSystemRegex, $systemName, $matches) !== false)
        {
            if(count($matches) > 0)
            {
                return $matches;
            }
        }

        return null;
    }



    public function getCubeSide($massCode)
    {
        return self::$sectorSize / pow(2, ord('h') - ord(strtolower($massCode)));
    }



    public function getSectorPosition($sectorName, $cubeSide)
    {
        $handAuthoredSectors = HandAuthoredSector::getAll();

        if(array_key_exists($sectorName, $handAuthoredSectors))
        {
            $position           = array();
            $position['x']      = floor($handAuthoredSectors[$sectorName][0] - $handAuthoredSectors[$sectorName][3]);
            $position['y']      = floor($handAuthoredSectors[$sectorName][1] - $handAuthoredSectors[$sectorName][3]);
            $position['z']      = floor($handAuthoredSectors[$sectorName][2] - $handAuthoredSectors[$sectorName][3]);

            $position['x']     -= ((($position['x'] + 65) % $cubeSide) + $cubeSide) % $cubeSide;
            $position['y']     -= ((($position['y'] + 25) % $cubeSide) + $cubeSide) % $cubeSide;
            $position['z']     -= ((($position['z'] + 1065) % $cubeSide) + $cubeSide) % $cubeSide;

            $position['size']   = $handAuthoredSectors[$sectorName][3];
            $position['ha']     = true;

            return $position;
        }
        else
        {
            $sectors = Sector::getAll();

            if(array_key_exists($sectorName, $sectors))
            {
                $position           = array();
                $position['x']      = $sectors[$sectorName][0];
                $position['y']      = $sectors[$sectorName][1];
                $position['z']      = $sectors[$sectorName][2];
                $position['size']   = self::$sectorSize;
                $position['ha']     = false;

                return $position;
            }
        }

        return null;
    }
}