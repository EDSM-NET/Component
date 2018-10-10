<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Friend
{
    private $_friends               = false;
    private $_pendingFriends        = false;
    private $_blockedFriends        = false;
    
    private $_countPendingFriends   = 0;
    
    public function getFriends()
    {
        if($this->_friends === false)
        {
            $temp   = self::getModel('Models_Users_Friends')->getFriendsByRefUser($this->getId());
            $result = array();

            foreach($temp AS $value)
            {
                if($value['refUser'] != $this->getId() && !in_array($value['refUser'], $result))
                {
                    $result[] = $value['refUser'];
                }
                if($value['refFriend'] != $this->getId() && !in_array($value['refFriend'], $result))
                {
                    $result[] = $value['refFriend'];
                }
            }
            
            $this->_friends = $result;
            
            unset($temp, $result);
        }
        
        return $this->_friends;
    }
    
    public function isFriend($refUser)
    {
        $friends = $this->getFriends();
        
        if(in_array($refUser, $friends))
        {
            return true;
        }
        
        return false;
    }
    
    public function getPendingFriends()
    {
        if($this->_pendingFriends === false)
        {
            $temp   = self::getModel('Models_Users_Friends')->getPendingByRefUser($this->getId());
            $result = array();

            foreach($temp AS $value)
            {
                if($value['refUser'] != $this->getId() && !in_array($value['refUser'], $result))
                {
                    $result[] = $value['refUser'];
                }
                if($value['refFriend'] != $this->getId() && !in_array($value['refFriend'], $result))
                {
                    $result[] = $value['refFriend'];
                }
                
                if($value['refFriend'] == $this->getId())
                {
                    $this->_countPendingFriends++;
                }
            }
            
            $this->_pendingFriends = $result;
            
            unset($temp, $result);
        }
        
        return $this->_pendingFriends;
    }
    
    public function isPendingFriend($refUser)
    {
        $friends = $this->getPendingFriends();
        
        if(in_array($refUser, $friends))
        {
            return true;
        }
        
        return false;
    }
    
    public function countIncomingPendingFriend()
    {
        if($this->_pendingFriends === false)
        {
            $this->getPendingFriends();
        }
        
        return $this->_countPendingFriends;
    }
    
    public function getBlockedFriends()
    {
        if($this->_blockedFriends === false)
        {
            $temp   = self::getModel('Models_Users_Friends')->getBlockedByRefUser($this->getId());
            $result = array();

            foreach($temp AS $value)
            {
                if($value['refUser'] != $this->getId() && !in_array($value['refUser'], $result))
                {
                    $result[] = $value['refUser'];
                }
                if($value['refFriend'] != $this->getId() && !in_array($value['refFriend'], $result))
                {
                    $result[] = $value['refFriend'];
                }
            }
            
            $this->_blockedFriends = $result;
            
            unset($temp, $result);
        }
        
        return $this->_blockedFriends;
    }
    
    public function isBlockedFriend($refUser)
    {
        if(in_array($refUser, $this->getBlockedFriends()))
        {
            return true;
        }
        
        return false;
    }
}