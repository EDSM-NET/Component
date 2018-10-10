<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

//TODO: Add friends detection directly into permissions methods...

namespace   Component\User;

trait PublicProfile
{
    public function getShowProfile()
    {
        if($this->getIdentity('showProfile') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShowCredits()
    {
        if($this->getIdentity('showCredits') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShowCreditsChart()
    {
        if($this->getIdentity('showCreditsChart') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShowDistancesHeatMap()
    {
        if($this->getIdentity('showDistancesHeatMap') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShowLogsHeatMap()
    {
        if($this->getIdentity('showLogsHeatMap') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShowLogsTimestamps()
    {
        if($this->getIdentity('showLogsTimestamps') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    
    
    public function getShareStatistics()
    {
        if($this->getIdentity('shareStatistics') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShareBadges()
    {
        if($this->getIdentity('shareBadges') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShareFleet()
    {
        if($this->getIdentity('shareFleet') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShareDiary()
    {
        if($this->getIdentity('shareDiary') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShareLogs()
    {
        if($this->getIdentity('shareLogs') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function getShareMap()
    {
        if($this->getIdentity('shareMap') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function waitScanBodyFromEDDN()
    {
        if($this->getIdentity('waitScanBodyFromEDDN') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function addFriendsFromJournal()
    {
        if($this->getIdentity('addFriendsFromJournal') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    
    
    /**
     * EMAILS
     */
    public function receiveEmailBadges()
    {
        if($this->getIdentity('receiveEmailBadges') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function receiveEmailWeeklyReports()
    {
        if($this->getIdentity('receiveEmailWeeklyReports') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function isMarkedForDeletion()
    {
        if($this->getIdentity('isMarkedForDeletion') == 1)
        {
            return true;
        }
        
        return false;
    }
    
    public function isMarkedForClearing()
    {
        if($this->getIdentity('isMarkedForClearing') == 1)
        {
            return true;
        }
        
        return false;
    }
}