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
 * The core Citrus class. It's used for setting up the environment and configuration
 * of the ORM. You shouldn't really be calling this class in your application code.
 */
class Base
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
     * The cached database connection.
     *
     * @access private
     * @var resource
     */
    private $_db;
    
    
    /**
     * A temporary placeholder of database configuration settings until we
     * implement full environment switching.
     * 
     * @access private
     * @var array
     */
    private $_config;


    /**
     * The settings to use to connect to the database.
     *
     * @access private
     * @var array
     */
    private $connectionSettings = array();


    /**
     * A temporary cache of the enumerated table results. We should then only
     * have to query the database once per table per request. Eventually this
     * will be moved into a proper stateless cache.
     *
     * @access private
     * @var array
     */
    private $_enumerated_tables = array();


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
     * Use this method to set up Citrus. This method expects an anonymous function
     * to be passed.
     *
     * @access public
     * @static
     * @param function $lambda
     * @return void
     */
    public static function config($lambda)
    {
        $lambda(self::getInstance());
    }


    /**
     * Returns an instance of ConnectionManager. Use this rather than going directly
     * to the method as things may change.
     *
     * @access public
     * @static
     * @return object
     */
    public static function connectionManager()
    {
        return ConnectionManager::getInstance();
    }


    /**
     * Adds a database connection.
     *
     * @access public
     * @param string $environment The environment to use this connection in. Can
     *                              be either 'development', 'production' or 'test'
     * @param array $connection The database connection
     * @return void
     * @todo Provide support for different environments and a MySQL cluster setup
     */
    public function addConnection($environment, $connection)
    {
        //ConnectionManager::addConnection($environment, $connection);

        $dsn = "{$connection['adapter']}:dbname={$connection['database']};host={$connection['host']};charset={$connection['encoding']}";
        $username = $connection['username'];
        $password = $connection['password'];

        $options = $connection;
        unset($options['adapter'], $options['database'], $options['host'], $options['encoding'], $options['username'], $options['password']);
        
        $this->_config = array(
            'dsn' => $dsn,
            'username' => $username,
            'password' => $password,
            'options' => $options,
        );
    }


    /**
     * Returns the currently active database connection.
     *
     * @access public
     * @return resource
     */
    public function getConnection()
    {
        if (!isset($this->_db)) {
            $this->_db = new \PDO($this->_config['dsn'], $this->_config['username'], $this->_config['password'], $this->_config['options']);
        }
        
        return $this->_db;
    }


    /**
     * Get the information about the table that we're working with. Only needs to
     * be called once, usually when we're creating the object.
     *
     * @access public
     * @param string $table The table to enumerate
     * @return array
     */
    public function enumerateTable($table)
    {
        if (array_key_exists($table, $this->_enumerated_tables)) {
            return $this->_enumerated_tables[$table];
        }

        $description = Query::__new()->describe($table);
        $this->_enumerated_tables[$table] = $description;
        return $description;
    }

}
