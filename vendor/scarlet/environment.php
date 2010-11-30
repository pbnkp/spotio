<?php
/**
 * Requires PHP 5.3
 * 
 * Scarlet : An event driven PHP framework.
 * Copyright (c) 2010, Matt Kirman <matt@mattkirman.com>
 * 
 * Licensed under the GPL license
 * Redistributions of files must retain the above copyright notice.
 * 
 * @copyright Copyright 2010, Matt Kirman <matt@mattkirman.com>
 * @package scarlet
 * @license GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>
 */
namespace Scarlet;
/**
 * Handles the entire Scarlet environment including configuration and plugins.
 */
class Environment
{
    
    const MODE_DEVELOPMENT = 0;
    const MODE_TESTING = 1;
    const MODE_PRODUCTION = 2;
    
    
    /**
     * The instance for the singleton.
     * 
     * @access private
     * @static
     * @var object
     */
    private static $_instance;
    
    
    /**
     * Stores the time that the processing was started.
     * 
     * @access private
     * @static
     * @var float
     */
    private static $_TIMESTART;
    
    
    /**
     * A record of config directives.
     * 
     * @access private
     * @var object
     */
    private $_config;
    
    
    /**
     * Initialises the application environment and loads the configuration.
     * 
     * @access private
     */
    private function __construct()
    {
        // Load the core config
        $this->loadConfig(CONFIG);
        
        if ($this->isDebug()) {
            self::$_TIMESTART = microtime(true);
        }
    }
    
    
    /**
     * This class should be a singleton.
     * 
     * @access public
     * @static
     * @return object self
     */
    public static function getInstance()
    {
        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        
        self::$_instance = new self();
        return self::$_instance;
    }
    
    
    /**
     * Loads all config files in the specified directory.
     * 
     * @access public
     * @param string $directory The config directory to load
     * @return void
     */
    public function loadConfig($directory)
    {
        $files = scandir($directory);
        $config = (array) $this->_config;
        
        foreach ($files as $file) {
            $f = $directory . DS . $file;
            if (!is_file($f)) continue;
            
            $components = explode('.', Inflector::underscore($file));
            $ext = array_pop($components);
            $asset = implode('.', $components);
            
            switch ($ext) {
                case 'php':
                    include $f;
                    break;
                
                case 'json':
                    $c = (array) json_decode(file_get_contents($f));
                    
                    if (!isset($config[$asset])) $config[$asset] = array();
                    $config[$asset] = (object) array_merge($config[$asset], $c);
                    break;
            }
        }
        
        $this->_config = (object) $config;
    }
    
    
    /**
     * Are we in debug mode?
     * 
     * @access public
     * @return bool
     */
    public function isDebug()
    {
        if ($this->getMode() === self::MODE_PRODUCTION) return false;
        return true;
    }
    
    
    /**
     * Returns the current application environment mode. Will be either Environment::MODE_TESTING,
     * Environment::MODE_DEVELOPMENT or Environment::MODE_PRODUCTION.
     * 
     * @access public
     * @param bool $text Whether to return the environment as an integer (default) or string
     * @return int
     */
    public function getMode($text=false)
    {
        $env = (isset($_SERVER['SCARLET_ENV'])) ? strtolower($_SERVER['SCARLET_ENV']) : false;
        
        switch ($env) {
            case 'testing':
                return ($text === true) ? "testing" : self::MODE_TESTING;
                break;
            
            case 'production':
                return ($text === true) ? "production" : self::MODE_PRODUCTION;
                break;
            
            default:
                return ($text === true) ? "development" : self::MODE_DEVELOPMENT;
                break;
        }
    }
    
    
    /**
     * Perform any final methods here. We use it primarily to output debug info.
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if ($this->isDebug() && php_sapi_name() != 'cli') {
            echo '<!-- ' . round(microtime(true) - self::$_TIMESTART, 4) . 's -->';
        }
    }
    
}