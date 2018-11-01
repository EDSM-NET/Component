<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait GalacticCoordinate
{
    public function getGalacticCoordinatesR()
    {
        if(!is_null($this->getX()))
        {
            return sqrt(
                  ($this->getX() / 32) * ($this->getX() / 32)
                + ($this->getY() / 32) * ($this->getY() / 32)
                + ($this->getZ() / 32) * ($this->getZ() / 32)
            );
        }

        return null;
    }

    public function getGalacticCoordinatesLongitude()
    {
        if(!is_null($this->getX()))
        {
            $atan = atan2( ($this->getX() / 32) , ($this->getZ() / 32) );
            return fmod(360 - ($atan * 180 / pi()),360);
        }

        return null;
    }

    public function getGalacticCoordinatesLatitude()
    {
        if(!is_null($this->getX()))
        {
            $asin = asin( ($this->getY() / 32) / $this->getGalacticCoordinatesR());
            return $asin *180 / pi();
        }

        return null;
    }

    public function getEquatorialCoordinatesDeclination()
    {
        if(!is_null($this->getX()))
        {
            return asin(
                  (
                      sin($this->getGalacticCoordinatesLatitude() * pi() / 180)
                    * sin(27.12825 * pi() / 180)
                  )
                + (
                      cos($this->getGalacticCoordinatesLatitude() * pi() / 180)
                    * cos(27.12825 * pi() / 180)
                    * sin(($this->getGalacticCoordinatesLongitude() - 32.93192) * pi() / 180))
            ) * 180 / pi();
        }

        return null;
    }

    public function getEquatorialCoordinatesDeclinationMinute()
    {
        if(!is_null($this->getX()))
        {
            return (int) (
                abs(
                        $this->getEquatorialCoordinatesDeclination() - (int) ($this->getEquatorialCoordinatesDeclination())
                ) * 60
            );
        }

        return null;
    }

    public function getEquatorialCoordinatesDeclinationSecond()
    {
        if(!is_null($this->getX()))
        {
            return (
                abs(
                        $this->getEquatorialCoordinatesDeclination() - (int) $this->getEquatorialCoordinatesDeclination()
                ) * 60
                - $this->getEquatorialCoordinatesDeclinationMinute() ) * 60;
        }

        return null;
    }

    public function getEquatorialCoordinatesRightAscension()
    {
        if(!is_null($this->getX()))
        {
            /*
            return (asin(
                  cos($this->getGalacticCoordinatesLatitude() * pi() / 180)
                * cos(($this->getGalacticCoordinatesLongitude() - 32.93192) * pi() / 180)
                / cos($this->getEquatorialCoordinatesDeclination() * pi() / 180)
            ) * 180 / pi()) + 192.85948;
            */

            return (atan2(
                cos($this->getGalacticCoordinatesLatitude()*pi()/180) * cos(($this->getGalacticCoordinatesLongitude()-32.93192)*pi()/180),
                (
                      sin($this->getGalacticCoordinatesLatitude()*pi()/180) * cos(27.12825*pi()/180)
                    - cos($this->getGalacticCoordinatesLatitude()*pi()/180) * sin(27.12825*pi()/180) * sin(($this->getGalacticCoordinatesLongitude()-32.93192)*pi()/180)
                )
            )) * 180 / pi() + 192.85948;
        }

        return null;
    }

    public function getEquatorialCoordinatesRightAscensionHour()
    {
        if(!is_null($this->getX()))
        {
            return (int) ($this->getEquatorialCoordinatesRightAscension() / 15);
        }

        return null;
    }

    public function getEquatorialCoordinatesRightAscensionMinute()
    {
        if(!is_null($this->getX()))
        {
            return (int) (
                (
                      ($this->getEquatorialCoordinatesRightAscension()  / 15)
                    - $this->getEquatorialCoordinatesRightAscensionHour()
                ) * 60
            );
        }

        return null;
    }

    public function getEquatorialCoordinatesRightAscensionSecond()
    {
        if(!is_null($this->getX()))
        {
            return (
                  (($this->getEquatorialCoordinatesRightAscension() / 15) - $this->getEquatorialCoordinatesRightAscensionHour()) * 60
                - $this->getEquatorialCoordinatesRightAscensionMinute()
            ) * 60;
        }

        return null;
    }
}