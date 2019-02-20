<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Coordinates
{
    static private $_estimatedCoordinates = array();

    public function getEstimatedCoordinates($maxUncertainty = 50)
    {
        return self::getEstimatedCoordinatesFromName($this->getName(), $maxUncertainty);
    }

    public function getEstimatedCoordinatesFromName($systemName, $maxUncertainty = 50)
    {
        if(!array_key_exists($systemName, self::$_estimatedCoordinates))
        {
            self::$_estimatedCoordinates[$systemName] = array();
        }
        if(array_key_exists($maxUncertainty, self::$_estimatedCoordinates[$systemName]))
        {
            return self::$_estimatedCoordinates[$systemName][$maxUncertainty];
        }

        $systemFragements = self::getFragmentsFromName($systemName);

        if(!is_null($systemFragements))
        {
            if(!array_key_exists('n1', $systemFragements) || is_null($systemFragements['n1']))
            {
                $systemFragements['n1'] = 0;
            }

            $cubeSide       = self::getCubeSide($systemFragements['mcode']);
            $uncertainty    = round(($cubeSide / 2 ) * sqrt(3));

            if($uncertainty > $maxUncertainty)
            {
                return null;
            }

            // Get coordinates inside sector
            $sOffset        = 17576 * (int) $systemFragements['n1'];
            $sOffset       +=   676 * (ord(strtoupper($systemFragements['l3'])) - ord('A'));
            $sOffset       +=    26 * (ord(strtoupper($systemFragements['l2'])) - ord('A'));
            $sOffset       +=         (ord(strtoupper($systemFragements['l1'])) - ord('A'));

            $estimatedZ     = floor($sOffset / 128**2);
            $sOffset       -= $estimatedZ * 128**2;

            $estimatedY     = floor($sOffset / 128);
            $sOffset       -= $estimatedY * 128;

            $estimatedX     = $sOffset;

            $estimatedX     = ($estimatedX * $cubeSide) + ($cubeSide / 2 );
            $estimatedY     = ($estimatedY * $cubeSide) + ($cubeSide / 2 );
            $estimatedZ     = ($estimatedZ * $cubeSide) + ($cubeSide / 2 );

            // Find sector position
            $sectorPosition = self::getSectorPosition($systemFragements['sector'], $cubeSide);

            $estimatedX = $estimatedX + $sectorPosition['x'];
            $estimatedY = $estimatedY + $sectorPosition['y'];
            $estimatedZ = $estimatedZ + $sectorPosition['z'];

            self::$_estimatedCoordinates[$systemName][$maxUncertainty] = array(
                'x'             => $estimatedX,
                'y'             => $estimatedY,
                'z'             => $estimatedZ,
                'uncertainty'   => $uncertainty,
            );

            return self::$_estimatedCoordinates[$systemName][$maxUncertainty];
        }

        return null;
    }
}