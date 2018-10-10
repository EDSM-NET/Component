<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Guild
{
    protected $_inGuilds = false;
    
    public function inGuilds()
    {
        if($this->_inGuilds === false)
        {
            $userGuilds          = self::getModel('Models_Guilds_Users')->getByRefUser($this->getId());
            $this->_inGuilds     = null;
            
            if(!is_null($userGuilds))
            {
                $guilds = array();

                foreach($userGuilds AS $guild)
                {
                    $guilds[] = \EDSM_Guild::getInstance($guild['refGuild']);
                }

                if(count($guilds) > 0)
                {
                    $this->_inGuilds = $guilds;
                }
            }
        }
        
        return $this->_inGuilds;
    }
}