<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Cargo
{
    protected $_cargo = false;
    
    public function getCargo()
    {
        if($this->_cargo === false)
        {
            $this->_cargo = self::getModel('Models_Users_Cargo')->getByRefUser($this->getId());
        }
        
        return $this->_cargo;
    }
    
    public function getNbCargo()
    {
        $count      = 0;
        $cargos     = $this->getCargo();
        
        if(is_array($cargos) && count($cargos) > 0)
        {
            foreach($cargos AS $cargo)
            {
                $count += (int) $cargo['total'];
            }
        }
            
        return $count;
    }
}