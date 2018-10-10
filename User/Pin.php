<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

trait Pin
{
    public function getCurrentPin()
    {
        $pinnedRecipe = $this->getIdentity('pinnedRecipe');
        
        if(!is_null($pinnedRecipe) && !empty($pinnedRecipe))
        {
            $pinnedRecipe       = explode('||', $pinnedRecipe);

            $return             = array();
            $return['type']     = $pinnedRecipe[0];
            $return['alias']    = (int) $pinnedRecipe[1];

            if($return['type'] == 'Blueprint')
            {
                if(array_key_exists(2, $pinnedRecipe))
                {
                    $return['grade']    = (int) $pinnedRecipe[2];
                }

                if(array_key_exists(3, $pinnedRecipe))
                {
                    $return['engineer']    = (int) $pinnedRecipe[3];
                }
            }

            if($return['type'] == 'BlueprintExperimentalEffect')
            {
                if(array_key_exists(2, $pinnedRecipe))
                {
                    $return['blueprint']    = (int) $pinnedRecipe[2];
                }

                if(array_key_exists(3, $pinnedRecipe))
                {
                    $return['grade']    = (int) $pinnedRecipe[3];
                }

                if(array_key_exists(4, $pinnedRecipe))
                {
                    $return['engineer']    = (int) $pinnedRecipe[4];
                }
            }

            return $return;
        }
        
        return null;
    }
    
    public function isPinned($type, $alias, $grade = null)
    {
        $currentPin = $this->getCurrentPin();
        
        if(!is_null($currentPin))
        {
            if($currentPin['type'] === $type && $currentPin['alias'] === $alias)
            {
                if(array_key_exists('grade', $currentPin) && !is_null($grade))
                {
                    if($currentPin['grade'] == $grade)
                    {
                        return true;
                    }
                }
                else
                {
                    return true;
                }
            }
        }
        
        return false;
    }
}