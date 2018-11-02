<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component;

abstract class Instance
{
    protected static $view          = null; //TODO: Is it used?!

    /**
     * Multiton implementation
     */
    protected static $instances     = array();

    public static function getInstance($id, $identity = null)
    {
        $id             = (int) $id;
        $className      = static::class;

        // Garbage removal
        foreach(self::$instances AS $class => $instances)
        {
            if(array_key_exists($class, self::$instances) && count(self::$instances[$class]) > 500)
            {
                self::$instances[$class] = array();
            }
        }

        if(!array_key_exists($className, self::$instances))
        {
            self::$instances[$className] = array();
        }

        if(!array_key_exists($id, self::$instances[$className]))
        {
            self::$instances[$className][$id]    = new $className($id, $identity);
        }

        return self::$instances[$className][$id];
    }

    public static function destroyInstance($id)
    {
        $id             = (int) $id;
        $className      = static::class;

        if(array_key_exists($className, self::$instances))
        {
            if(array_key_exists($id, self::$instances[$className]))
            {
                unset(self::$instances[$className][$id]);
            }
        }
    }

    /**
     * Default constructor to autopopulate instance
     *
     * @param type $id
     * @param type $identity
     * @return type
     */
    protected function __construct($id, $identity)
    {
        $this->_id = (int) $id;

        if(is_null($id))
        {
            return;
        }

        // Override default populate in case we want to avoid database call
        if(!is_null($identity))
        {
            if(is_array($identity) && $identity[$this->_primaryKey] == $id)
            {
                $this->_identity = $identity;
                return;
            }
        }

        // Populate IDENTITY based on default model from class
        $this->_identity = self::getModel($this->_defaultModel)->getById( $this->_id );
    }

    protected function __clone(){}

    /**
     * Database Models
     */
    protected static $models = array();
    protected function getModel($model)
    {
        if(!array_key_exists($model, self::$models))
        {
            self::$models[$model] = new $model;
        }

        return self::$models[$model];
    }

    /**
     * Cache
     */
    protected static $caches = array();
    protected function getCache($cache)
    {
        if(!array_key_exists($cache, self::$caches))
        {
            $bootstrap              = \Zend_Registry::get('Zend_Application');
            $cacheManager           = $bootstrap->getResource('cachemanager');
            self::$caches[$cache]   = $cacheManager->getCache($cache);
        }

        return self::$caches[$cache];
    }


    /**
     * Identity
     */
    protected $_id;
    protected $_identity;

    public function isValid()
    {
        if(is_null($this->_identity) || count($this->_identity) == 0)
        {
            return false;
        }

        return true;
    }

    public function getId()
    {
        return $this->_id;
    }

    protected function getIdentity($key = null)
    {
        if(!is_null($key))
        {
            if(is_array($this->_identity) && array_key_exists($key, $this->_identity))
            {
                return $this->_identity[$key];
            }

            return null;
        }

        return $this->_identity;
    }
}