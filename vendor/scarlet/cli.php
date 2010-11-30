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
 * Provides CLI functions for Scarlet.
 */
class Cli
{
    
    /**
     * Object instance.
     * 
     * @access private
     * @static
     * @var object CLI
     */
    private static $_instance;
    
    
    /**
     * The application environment
     * 
     * @access private
     * @var object Environment
     */
    private $_environment;
    
    
    /**
     * Parameters passed to the CLI.
     * 
     * @access private
     * @var array
     */
    private $_params = array();
    
    
    /**
     * Have we initialised the application?
     * 
     * @access private
     * @var bool
     */
    private $_booted = false;
    
    
    /**
     * Tasks for the CLI.
     * 
     * @access private
     * @var array
     */
    private $_tasks = array();
    
    
    /**
     * Helps us to format the help pages.
     * 
     * @access private
     * @var int
     */
    private $_longest_task_name = 0;
    
    
    /**
     * Initialises the CLI. This class is a singleton, so use getInstance() instead.
     * 
     * @access private
     * @param Environment $environment The application environment
     */
    private function __construct(Environment $environment)
    {
        $this->_environment =& $environment;
        
        $params = $_SERVER['argv'];
        $script = array_shift($params);
        $this->_params = $params;
    }
    
    
    /**
     * Returns an instance of the CLI.
     * 
     * @access public
     * @static
     * @param object $environment The application environment. Optional
     * @return object Cli
     */
    public static function getInstance($environment = false)
    {
        if (!isset(self::$_instance))
            self::$_instance = new Cli($environment);
        
        return self::$_instance;
    }
    
    
    /**
     * Initialises the application, loading all the relevant scripts and commands.
     * 
     * @access public
     * @return void
     */
    public function boot()
    {
        if ($this->_booted) throw new \Exception('The CLI is already booted.');
        
        // Load the built-in Scarlet tasks
        $dir = SCARLET . DS . "cli" . DS . "tasks";
        $tasks = scandir($dir);
        foreach ($tasks as $task) {
            if ($task == '.' || $task == '..') continue;
            require_once $dir . DS . $task;
        }
        
        
        // And, finally, any user specific tasks in lib/tasks
        $dir = ROOT . DS . "lib" . DS . "tasks";
        if (file_exists($dir)) {
            $tasks = scandir($dir);
            foreach ($tasks as $task) {
                if ($task == '.' || $task == '..') continue;
                require_once $dir . DS . $task;
            }
        }
        
        
        $this->_booted = true;
    }
    
    
    /**
     * This is the main program loop.
     * 
     * @access public
     * @return void
     */
    public function handle()
    {
        if (!$this->_is_booted) $this->boot();
        
        // If we haven't specified a task to carry out, then show the help page
        if (empty($this->_params)) $this->showHelp();
        
        // We're trying to run a task, so execute it if it exists otherwise
        // show that we don't recognise the task and exit.
        $params = $this->_params;
        $task = array_shift($params);
        
        if (array_key_exists($task, $this->_tasks)) {
            $this->_tasks[$task]->execute($params);
        } else {
            echo "scarlet: '{$task}' is not a scarlet task. See 'scarlet'.";
            exit;
        }
    }
    
    
    /**
     * Adds a new task to the queue. Use this method in your scripts.
     * 
     * @access public
     * @static
     * @param string $name The name of the task
     * @param string $description The description of your task.
     * @param lambda $lambda The function to execute when carrying out the task.
     * @return mixed
     */
    public static function task($name, $description, $lambda)
    {
        return self::getInstance()->addTask($name, $description, $lambda);
    }
    
    
    /**
     * Adds a new task to the queue
     * 
     * @access public
     * @param string $name The name of the task
     * @param string $description The description of your task.
     * @param lambda $lambda The function to execute when carrying out the task.
     * @return mixed
     */
    public function addTask($name, $description, $lambda)
    {
        $this->_tasks[$name] = new Cli\Task($name, $description, $lambda);
        
        if (strlen($name) > $this->_longest_task_name)
            $this->_longest_task_name = strlen($name);
    }
    
    
    /**
     * Shows the help page and exits.
     * 
     * @access private
     * @return void
     */
    private function showHelp()
    {
        $tasks = $this->_tasks;
        ksort($tasks);
        
        $padding = $this->_longest_task_name + strlen("scarlet ") + 2;
        
        $output = array();
        foreach ($tasks as $name => $task) {
            $desc = $task->getDescription();
            $output[] = str_pad("scarlet {$name}", $padding) . "# {$desc}";
        }
        echo implode("\n", $output);
        
        exit;
    }
    
    
    /**
     * Destructor. Does some tidying up.
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        echo "\n";
    }
    
}