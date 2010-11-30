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
 * @subpackage citrus
 * @license GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>
 */
namespace Citrus;
/**
 * The Citrus connection manager. Handles caching of database connections, switching
 * between master/slaves and failover.
 */
class ConnectionManager
{

    /**
     * The instance for the singleton.
     *
     * @access private
     * @static
     * @var object
     */
    private static $_instance;

    
    /**
     * This class is a singleton. Use getInstance();
     *
     * @access private
     */
    private function __construct()
    {

    }

    /**
     * This class is a singleton. Use this method instead of __construct();
     *
     * @access public
     * @static
     * @return object $this
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
     * Currently maps to Base::getConnection. It won't in the future.
     *
     * @access public
     * @return resource
     */
    public function getConnection()
    {
        return Base::getInstance()->getConnection();
    }

}
