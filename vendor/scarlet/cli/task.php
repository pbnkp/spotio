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
namespace Scarlet\Cli;
/**
 * Provides CLI functions for Scarlet.
 */
class Task
{
    
    /**
     * The name of the task.
     * 
     * @access private
     * @var string
     */
    private $_name;
    
    
    /**
     * The description of the task.
     * 
     * @access private
     * @var string
     */
    private $_description;
    
    
    /**
     * The lambda function to execute.
     * 
     * @access private
     * @var lambda
     */
    private $_lambda;
    
    
    /**
     * Constructor.
     * 
     * @access public
     * @param string $name The name of the task
     * @param string $description The description of the task
     * @param lambda $lambda The function to execute
     */
    public function __construct($name, $description, $lambda)
    {
        $this->_name = $name;
        $this->_description = $description;
        $this->_lambda = $lambda;
    }
    
    
    /**
     * Returns the task name.
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    
    /**
     * Returns the task's description.
     * 
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
    
    
    /**
     * Executes the task.
     * 
     * @access public
     * @param array $params An array of parameters passed to the task
     * @return void
     */
    public function execute($params=array()){
        $lambda = $this->_lambda;
        $lambda($this, $params);
    }
    
}
