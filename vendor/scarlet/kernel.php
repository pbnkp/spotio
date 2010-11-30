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
 * The kernel is the heart of the Scarlet system. It manages an environment that
 * can host bundles.
 */
class Kernel
{
    
    /**
     * The application environment.
     * 
     * @access private
     * @var bool
     */
    private $_environment;
    
    
    /**
     * Debug status of the application.
     * 
     * @access private
     * @var bool
     */
    private $_debug;
    
    
    /**
     * Stores the status of the kernel.
     * 
     * @access private
     * @var bool
     */
    private $_booted;
    
    
    /**
     * Initialises the kernel.
     * 
     * @access public
     * @param Environment $environment The application environment
     * @return object $this
     */
    public function __construct(Environment $environment)
    {
        $this->_environment =& $environment;
        $this->_debug = $this->_environment->isDebug();
        $this->_booted = false;
        
        if ($this->_debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            ini_set('display_errors', 0);
        }
    }
    
    
    /**
     * Is the kernel running?
     * 
     * @access public
     * @return bool
     */
    public function isBooted()
    {
        return $this->_booted;
    }
    
    
    /**
     * Initialises the kernel and any plugins.
     * 
     * @access public
     * @return void
     */
    public function boot()
    {
        if ($this->_booted) throw new \Exception('The kernel is already booted.');
        
        // Boot the plugins
        
        
        
        // Now calculate the routes
        $Router = Router::getInstance();
        $Router->execute();
        
        $this->_booted = true;
    }
    
    
    /**
     * Shutdowns the kernel. Mostly useful when running tests.
     * 
     * @access public
     * @return void
     */
    public function shutdown()
    {
        $this->_booted = false;
        
    }
    
    
    /**
     * Restarts the kernel. Mostly useful when running tests.
     * 
     * @access public
     * @return void
     */
    public function restart()
    {
        $this->shutdown();
        $this->boot();
    }
    
    
    /**
     * This is the main program loop.
     * 
     * @access public
     * @return void
     */
    public function handle()
    {
        if ($this->_booted == false) {
            $this->boot();
        }
        
        $Router = Router::getInstance();
        $Controller = $Router->getController();

        if ($Controller === false) { // We couldn't find a valid route
            if ($this->_debug) {
                throw new \Exception("Couldn't find a valid route");
            } else {
                echo file_get_contents(PUBLIC_DIR . DS . '404.html');
                exit;
            }
        }

        $Controller = new $Controller();
        $Controller->params = $Router->getParams();
        $Controller->format = $Router->getFormat();
        
        
        // Run the action
        if (method_exists($Controller, $Router->getAction())) {
            $return_value = call_user_func_array(array($Controller, $Router->getAction()), array());
        } else {
            // We can't find the controller/action. If we're in debug mode then
            // print out the exception to screen, otherwise just show a 404 page.
            if ($this->_debug) {
                throw new \Exception("{$Router->getController()} doesn't implement {$Router->getAction()}()");
            } else {
                echo file_get_contents(PUBLIC_DIR . DS . '404.html');
                exit;
            }
        }
        
        // If we have returned a boolean false then we should display the 404 page
        if ($return_value === false) {
            echo file_get_contents(PUBLIC_DIR . DS . '404.html');
            exit;
        }
        
        
        // Render the view.
        $View = new View($Controller, $Controller->getView(), $Router->getFormat());
        
        // And then the layout.
        $Layout = new View($Controller, $Controller->getLayout(), $Router->getFormat(), 'layout', $View->content(true));
        
        echo $Layout->content();
    }
    
}
