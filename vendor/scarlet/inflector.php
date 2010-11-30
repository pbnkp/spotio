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
 * String conversion, y'all
 */
class Inflector
{
    
    /**
     * This is a static class.
     * 
     * @access private
     */
    private function __construct(){}
    
    
    /**
     * Converts a string to underscored.
     * 
     * @access public
     * @static
     * @param string $string The string
     * @return string
     */
    public static function underscore($string)
    {
        $string[0] = strtolower($string[0]);
        
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        $underscored = preg_replace_callback('/([A-Z])/', $func, $string);
        
        if ($underscored[0] == '_') $underscored = substr($underscored, 1);
        return $underscored;
    }
    
    
    /**
     * Converts a string to a camelcased format.
     * 
     * @access public
     * @static
     * @param string $string The string
     * @param bool $capitalise_first_char Whether to capitalise the first character. Defaults to true.
     * @return string
     */
    public static function camelize($string, $capitalise_first_char=true)
    {
        if ($capitalise_first_char) $string[0] = strtoupper($string[0]);
        $func = create_function('$c', 'return strtoupper($c[1]);');
        
        $camelCased = preg_replace_callback('/_([a-z])/', $func, $string);
        return $camelCased;
    }
    
}
