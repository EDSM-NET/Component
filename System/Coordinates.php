<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Coordinates
{
    private $_estimatedCoordinates = array();
    public function getEstimatedCoordinates($maxUncertainty = 50)
    {
        if(array_key_exists($maxUncertainty, $this->_estimatedCoordinates))
        {
            return $this->_estimatedCoordinates[$maxUncertainty];
        }

        $systemFragements = $this->getFragmentsFromName();

        if(!is_null($systemFragements))
        {
            if(!array_key_exists('n1', $systemFragements) || is_null($systemFragements['n1']))
            {
                $systemFragements['n1'] = 0;
            }

            $cubeSide       = $this->getCubeSide($systemFragements['mcode']);
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
            $sectorPosition = $this->getSectorPosition($systemFragements['sector'], $cubeSide);

            $estimatedX = $estimatedX + $sectorPosition['x'];
            $estimatedY = $estimatedY + $sectorPosition['y'];
            $estimatedZ = $estimatedZ + $sectorPosition['z'];

            $this->_estimatedCoordinates[$maxUncertainty] = array(
                'x'             => $estimatedX,
                'y'             => $estimatedY,
                'z'             => $estimatedZ,
                'uncertainty'   => $uncertainty,
            );

            return $this->_estimatedCoordinates[$maxUncertainty];
        }

        return null;
    }
}