<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component;

class User extends Instance
{
    use \Component\User\Activity;
    use \Component\User\Badge;
    use \Component\User\Cargo;
    use \Component\User\Credit;
    use \Component\User\Data;
    use \Component\User\Exploration;
    use \Component\User\Faction;
    use \Component\User\Friend;
    use \Component\User\Guild;
    use \Component\User\HoloMe;
    use \Component\User\Locale;
    use \Component\User\Material;
    use \Component\User\Mission;
    use \Component\User\Pin;
    use \Component\User\Power;
    use \Component\User\PublicProfile;
    use \Component\User\Rank;
    use \Component\User\Reputation;
    use \Component\User\Role;
    use \Component\User\Ship;

    protected $_defaultModel    = 'Models_Users';
    protected $_primaryKey      = 'id';

    //TODO: Remove cache here
    static protected $cache = null;

    public function getSaltedPassword()
    {
        if(!$this->isValid())
        {
            return null;
        }

        return $this->getIdentity('password');
    }

    public function getCMDR()
    {
        if(!$this->isValid())
        {
            return 'Guest';
        }

        return $this->getIdentity('commanderName');
    }

    public function getEmail()
    {
        return $this->getIdentity('email');
    }

    public function getPlatform()
    {
        $platform = $this->getIdentity('platform');

        // Stay compatible with the code, but need PC to catch the new UNIQUE index
        if($platform == 'PC')
        {
            return null;
        }

        return $this->getIdentity('platform');
    }

    public function getConfirmationString()
    {
        return $this->getIdentity('confirmation_string');
    }

    public function havePassword()
    {
        $password = $this->getIdentity('password');

        if(!is_null($password))
        {
            return true;
        }

        return false;
    }



    /**
     * LINKED ACCOUNT
     */
    protected $_linkedAccount = false;
    public function getLinkedAccounts()
    {
        if($this->_linkedAccount === false)
        {
            $linkedAccounts         = self::getModel('Models_Users_Links')->getByRefUser($this->getId());
            $this->_linkedAccount   = null;

            if(!is_null($linkedAccounts))
            {
                $tempAccounts = array();

                foreach($linkedAccounts AS $linkedAccount)
                {
                    if($linkedAccount['confirmed'] == 1)
                    {
                        if($linkedAccount['refUser'] == $this->getId())
                        {
                            $tempUser = \Component\User::getInstance($linkedAccount['refLink']);
                        }
                        else
                        {
                            $tempUser = \Component\User::getInstance($linkedAccount['refUser']);
                        }

                        if($tempUser->isValid() && $tempUser->getRole() != 'guest')
                        {
                            if(!in_array($tempUser->getId(), $tempAccounts))
                            {
                                $tempAccounts[] = $tempUser->getId();
                            }
                        }
                    }
                }

                if(count($tempAccounts) > 0)
                {
                    $this->_linkedAccount = $tempAccounts;
                }
            }
        }

        return $this->_linkedAccount;
    }



    /**
     * API
     */
    public function getApiKey()
    {
        if(!empty($this->getIdentity('apiKey')))
        {
            return $this->getIdentity('apiKey');
        }

        return null;
    }

    public function useNewJournalApi()
    {
        if($this->getIdentity('useNewJournalApi') == 1)
        {
            return true;
        }

        return false;
    }

    /**
     * Generate a unique color based on user PRIMARY id
     *
     * @param type $minimumBrightness
     * @param type $spec
     * @return int
     */
    public function getColor($minimumBrightness = 100, $spec = 10)
    {
        $spec                   = min(max(0, $spec), 10);
        $minimumBrightness      = min(max(0, $minimumBrightness), 255);

        $hash                   = md5('EDSM' . $this->getId());
        $colors                 = array();

        //convert hash into 3 decimal values between 0 and 255
        for($i = 0; $i < 3; $i++)
        {
            $colors[$i] = max(
                array(
                    round(((hexdec(substr($hash, $spec * $i, $spec))) / hexdec(str_pad('', $spec, 'F'))) * 255),
                    $minimumBrightness
                )
            );
        }


        if($minimumBrightness > 0)
        {
            // Loop until brightness is above or equal to minimumBrightness
            while((array_sum($colors) / 3) < $minimumBrightness)
            {
                for($i = 0; $i < 3; $i++)
                {
                    $colors[$i] += 10; //increase each color by 10
                }
            }
        }

        return $colors;
    }
}