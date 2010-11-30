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
 * Scarlet relies on namespaces to define the layout of the codebase. This class
 * provides the ability to autoload those classes.
 */
class Autoloader
{
    
    private static $autoloaders = array();
    
    
    /**
     * Converts a namespaced class into a file location and loads it.
     * 
     * @access public
     * @static
     * @param string $name The name of the class to load
     * @return void
     */
    public static function load($name)
    {
        $name = explode('\\', $name);
        if (count($name) < 2) throw new \Exception("Malformed class name");
        if (empty($name[0])) array_shift($name);
        $type = strtolower($name[0]);
        
        $name_0 = Inflector::underscore($name[0]);
        unset($name[0]);
        
        foreach ($name as $k => $v) {
            $name[$k] = Inflector::underscore($v);
        }
        $formatted_name = implode('/', $name);
        
        $file = (array_key_exists($type, self::$autoloaders)) ?
                    str_replace('{name}', $formatted_name, self::$autoloaders[$type]) :
                    PLUGINS . DS . $name_0 . DS . $formatted_name;
        
        $file .= '.php';
        
        if (!file_exists($file)) {
            throw new \Exception("File '$file' not found");
        }
        
        require_once $file;
    }
    
    
    /**
     * Add an autoloader structure. This allows us to easily extend the Scarlet
     * app structure.
     * 
     * @access public
     * @static
     * @param string $type The top level namespace e.g. scarlet, app, citrus, etc.
     * @param string $structure The directory structure to load this file. Use {name}
     *                          as a substitute for the class name
     * @return void
     */
     public static function add($type, $structure)
     {
         self::$autoloaders[strtolower($type)] = $structure;
     }
    
}


spl_autoload_register('\Scarlet\Autoloader::load');
Autoloader::add('scarlet', SCARLET . DS . '{name}');
Autoloader::add('app', APP . DS . '{name}');
