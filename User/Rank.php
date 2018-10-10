<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Rank
{
    private $_ranks = false;
    
    public function getRanks()
    {
        if($this->_ranks === false)
        {
            $this->_ranks = self::getModel('Models_Users_Ranks')->getByRefUser($this->getId());
        }
        
        return $this->_ranks;
    }
    
    public function getRankCombat()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('combat', $rank))
        {
            $rank = (int) $rank['combat'];
            
            if($rank >= 4)
            {
                $this->giveBadge(25);
            }
            if($rank >= 8)
            {
                $this->giveBadge(30);
            }
            
            return $rank;
        }
        
        return 0;
    }
    
    public function getRankCombatProgress()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('combatProgress', $rank))
        {
            return (int) $rank['combatProgress'];
        }
        
        return 0;
    }
    
    public function getRankTrader()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('trader', $rank))
        {
            $rank = (int) $rank['trader'];
            
            if($rank >= 4)
            {
                $this->giveBadge(15);
            }
            if($rank >= 8)
            {
                $this->giveBadge(20);
            }
            
            return $rank;
        }
        
        return 0;
    }
    
    public function getRankTraderProgress()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('traderProgress', $rank))
        {
            return (int) $rank['traderProgress'];;
        }
        
        return 0;
    }
    
    public function getRankExplorer()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('explorer', $rank))
        {
            $rank = (int) $rank['explorer'];
            
            if($rank >= 4)
            {
                $this->giveBadge(5);
            }
            if($rank >= 8)
            {
                $this->giveBadge(10);
            }
            
            return $rank;
        }
        
        return 0;
    }
    
    public function getRankExplorerProgress()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('explorerProgress', $rank))
        {
            return (int) $rank['explorerProgress'];
        }
        
        return 0;
    }
    
    public function getRankCQC()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('CQC', $rank))
        {
            $rank = (int) $rank['CQC'];
            
            if($rank >= 4)
            {
                $this->giveBadge(35);
            }
            if($rank >= 8)
            {
                $this->giveBadge(40);
            }
            
            return $rank;
        }
        
        return 0;
    }
    
    public function getRankCQCProgress()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('CQCProgress', $rank))
        {
            return (int) $rank['CQCProgress'];
        }
        
        return 0;
    }
    
    public function getRankFederation()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('federation', $rank))
        {
            return (int) $rank['federation'];
        }
        
        return 0;
    }
    
    public function getRankFederationProgress()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('federationProgress', $rank))
        {
            return (int) $rank['federationProgress'];
        }
        
        return 0;
    }
    
    public function getRankEmpire()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('empire', $rank))
        {
            return (int) $rank['empire'];
        }
        
        return 0;
    }
    
    public function getRankEmpireProgress()
    {
        $rank = $this->getRanks();
        
        if(!is_null($rank) && array_key_exists('empireProgress', $rank))
        {
            return (int) $rank['empireProgress'];
        }
        
        return 0;
    }
}