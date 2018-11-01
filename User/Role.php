<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Role
{
    use \Config\Secret\User\DistanceTeam;
    use \Config\Secret\User\EditorTeam;
    use \Config\Secret\User\GalacticMappingTeam;
    use \Config\Secret\User\GalnetTeam;
    use \Config\Secret\User\Patreon;
    use \Config\Secret\User\Streamer;

    private $_donations = false;

    public function getRole()
    {
        if($this->isValid() === false)
        {
            return 'guest';
        }

        if($this->getIdentity('valid') <= 0)
        {
            return 'guest';
        }

        if($this->isAdmin())
        {
            return 'admin';
        }

        if($this->isMaster())
        {
            return 'master';
        }

        return 'member';
    }

    public function isStreamer()
    {
        if(array_key_exists($this->getId(), self::$streamer))
        {
            return true;
        }

        return false;
    }



    public function getDonations()
    {
        if($this->_donations === false)
        {
            $this->_donations = self::getModel('Models_Users_Donations')->getbyRefUser($this->getId());
        }

        return $this->_donations;
    }

    public function isBenefactor()
    {
        // Guests
        if($this->getRole() == 'guest')
        {
            return false;
        }

        // Admin and team members
        if($this->isGalacticTeam() === true)
        {
            $this->giveBadge(9500);
            return true;
        }

        // Patreons
        if(in_array($this->getId(), self::$patreon))
        {
            $this->giveBadge(9500);
            return true;
        }

        // Regular members
        $donations = $this->getDonations();

        if(!is_null($donations))
        {
            $this->giveBadge(9500);
            return true;
        }

        return false;
    }

    public function haveAds()
    {
        if($this->getRole() == 'guest')
        {
            return true;
        }

        /**
         * ONLY REGULAR MEMBER
         */
        if($this->getRole() == 'member')
        {
            $amountPerMonth = 1;
            $donations      = $this->getDonations();

            if(!is_null($donations) && count($donations) > 0 && $this->isBenefactor() === true)
            {
                $donationsTime  = null;

                foreach($donations AS $donation)
                {
                    if($donation['amount'] >= $amountPerMonth)
                    {
                        $currentDonationTime = strtotime($donation['dateDonation']);

                        if(is_null($donationsTime) || $donationsTime < $currentDonationTime)
                        {
                            $donationsTime = $currentDonationTime;
                        }

                        $donationsTime += round((float) $donation['amount'] / $amountPerMonth * 86400 * 365 / 12);
                    }
                }

                if($donationsTime > time())
                {
                    return false;
                }
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    public function isAdmin()
    {
        if($this->isValid() === false)
        {
            return false;
        }

        if($this->isMaster())
        {
            return true;
        }

        if($this->getIdentity('role') == 'admin')
        {
            return true;
        }

        return false;
    }

    public function canEditInformation()
    {
        if($this->isGalacticTeam())
        {
            return true;
        }

        if($this->isAdmin())
        {
            return true;
        }

        if($this->isMaster())
        {
            return true;
        }

        if(in_array($this->getId(), self::$editorTeam))
        {
            return true;
        }

        return false;
    }

    public function canEditDistances()
    {
        if($this->isAdmin())
        {
            return true;
        }

        if($this->isMaster())
        {
            return true;
        }

        if(in_array($this->getId(), self::$distanceTeam))
        {
            return true;
        }

        return false;
    }

    public function canEditGalnet()
    {
        if($this->isGalacticTeam())
        {
            return true;
        }

        if($this->isAdmin())
        {
            return true;
        }

        if($this->isMaster())
        {
            return true;
        }

        if(in_array($this->getId(), self::$galnetTeam))
        {
            return true;
        }

        return false;
    }

    public function isGalacticTeam()
    {
        if($this->isAdmin())
        {
            return true;
        }

        if($this->isMaster())
        {
            return true;
        }



        if(in_array($this->getId(), self::$galacticMappingTeam))
        {
            return true;
        }

        return false;
    }

    public function isMaster()
    {
        $masters = array(
            952,    // Anthor
        );

        if(in_array($this->getId(), $masters))
        {
            return true;
        }

        return false;
    }
}