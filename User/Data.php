<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

use         Alias\Commander\Data    AS AliasData;

trait Data
{
    protected $_data = false;
    
    public function getData()
    {
        if($this->_data === false)
        {
            $this->_data = self::getModel('Models_Users_Data')->getByRefUser($this->getId());
        }
        
        return $this->_data;
    }
    
    public function getNbData()
    {
        $count  = 0;
        $max    = 0;
        $datas  = $this->getData();
        
        if(is_array($datas) && count($datas) > 0)
        {
            foreach($datas AS $data)
            {
                $count += (int) $data['total'];
                
                if((int) $data['total'] > 0)
                {
                    $max    += AliasData::getMax($data['type']);
                }
            }
        }
        
        if($count >= 1)
        {
            $this->giveBadge(2600);
            
            if($count >= 250)
            {
                $this->giveBadge(2630);
            }
        }
            
        return array(
            'count' => $count,
            'max'   => $max,
        );
    }
}