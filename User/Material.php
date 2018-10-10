<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

//TODO: Check materials badge to be put here too!

namespace   Component\User;

use         Alias\Commander\Material    AS AliasMaterial;

trait Material
{
    protected $_materials = false;
    
    public function getMaterials()
    {
        if($this->_materials === false)
        {
            $this->_materials = self::getModel('Models_Users_Materials')->getByRefUser($this->getId());
        }
        
        return $this->_materials;
    }
    
    public function getNbMaterials()
    {
        $count      = 0;
        $max        = 0;
        $materials  = $this->getMaterials();
        
        if(is_array($materials) && count($materials) > 0)
        {
            foreach($materials AS $material)
            {
                $count  += (int) $material['total'];
                
                if((int) $material['total'] > 0)
                {
                    $max    += AliasMaterial::getMax($material['type']);
                }
            }
        }
            
        return array(
            'count' => $count,
            'max'   => $max,
        );
    }
}