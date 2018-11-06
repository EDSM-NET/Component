<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Reputation
{
    private $_reputations = false;

    public function getReputions()
    {
        if($this->_reputations === false)
        {
            $this->_reputations = self::getModel('Models_Users_Reputations')->getByRefUser($this->getId());
        }

        return $this->_reputations;
    }

    public function getReputationFederation()
    {
        $reputation = $this->getReputions();

        if(!is_null($reputation) && array_key_exists('federation', $reputation))
        {
            return $reputation['federation'];
        }

        return 0;
    }

    public function getReputationEmpire()
    {
        $reputation = $this->getReputions();

        if(!is_null($reputation) && array_key_exists('empire', $reputation))
        {
            return $reputation['empire'];
        }

        return 0;
    }

    public function getReputationIndependent()
    {
        $reputation = $this->getReputions();

        if(!is_null($reputation) && array_key_exists('independent', $reputation))
        {
            return $reputation['independent'];
        }

        return 0;
    }

    public function getReputationAlliance()
    {
        $reputation = $this->getReputions();

        if(!is_null($reputation) && array_key_exists('alliance', $reputation))
        {
            return $reputation['alliance'];
        }

        return 0;
    }
}