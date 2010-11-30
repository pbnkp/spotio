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
 * The core Controller class. This is the class that all controllers in your
 * application should inherit from.
 */
class Controller
{
    
    public $data = array();
    public $params = array();
    public $format = array();
    
    
    protected $layout = 'application';
    protected $view;
    
    
    /**
     * The helpers that the views should use. Some common helpers that you may want
     * to include are \Scarlet\Helpers\Html and \Scarlet\Helpers\Form
     * 
     * @access protected
     * @var array
     */
    protected $helpers = array();
    
    
    protected $beforeFilters = array();
    protected $afterFilters = array();
    
    
    /**
     * You shouldn't be using your own constructor. However, if you decide that
     * you need to, make sure that you run parent::__construct() before you run
     * any of your own code.
     * 
     * @access public
     * @return object
     */
    public function __construct()
    {
        if (method_exists($this, '__startup')) $this->__startup();
        $this->runFilters($this->beforeFilters);
    }
    
    
    /**
     * You shouldn't be using your own destructor. However, if you decide that
     * you need to, make sure you run parent::__destruct() after your own code.
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->runFilters($this->afterFilters);
        if (method_exists($this, '__shutdown')) $this->__shutdown();
    }


    /**
     * Runs a specific set of filters. If any filters return boolean 'false' then
     * execution is stopped.
     *
     * @access protected
     * @final
     * @param array $filters The filters to run
     * @return void
     */
    final protected function runFilters($filters)
    {
        if (empty($filters)) return;

        foreach ($filters as $filter) {
            if (is_string($filter)) {
                // This is a filter that is run on every request.
                $this->runFilter($filter);
            } elseif (is_array($filter)) {
                // This is a filter that has options
                
            }
        }
    }


    /**
     * Runs a single filter. If a filter returns boolean 'false' then execution
     * is stopped. Filters can either exist in the controller (or a controller that
     * your code inherits from) or in a separate class. We only support static
     * methods for filters defined outside of the controller.
     *
     * @access protected
     * @final
     * @param mixed $filter The filter to run
     * @return void
     */
    final protected function runFilter($filter)
    {
        if (strpos($filter, '::')) {
            // This filter is defined in another class
            list($class, $method) = explode('::', $filter);
            if (strpos($class, '\\') === false) $class = '\\' . __NAMESPACE__ . '\\' . $class;
            $action = $class . '::' . $method;
        } else {
            $class = $this;
            $method = $filter;
            $action = array($this, $filter);
        }

        if (method_exists($class, $method)) {
            $response = call_user_func_array($action, array());
            if ($response === false) exit;
        } else {
            // Fail quietly, for now
        }
    }
    
    
    /**
     * Returns the name of the layout that we want to sue for this particular action.
     * Defaults to 'application'.
     * 
     * @access public
     * @final
     * @return string The name of the layout
     */
    final public function getLayout()
    {
        return $this->layout;
    }
    
    
    /**
     * Returns the name of the view that we want to use for this particular action.
     * If Controller::view is undefined then we fall back to the name of the action.
     * 
     * @access public
     * @final
     * @return string The name of the view
     */
    final public function getView()
    {
        if (isset($this->view)) {
            if (strpos($this->view, '/') === false) {
                return Router::getInstance()->getController(false) . '/' . $this->view;
            }
            return $this->view;
        }
        
        $Router = Router::getInstance();
        return $Router->getController(false) . '/' . $Router->getAction();
    }


    /**
     * Returns the helpers that need to be loaded for the view. Default helpers are
     * '\Scarlet\Helpers\Html' and '\Scarlet\Helpers\Form'.
     *
     * @access public
     * @final
     * @return array The helpers to load
     */
    final public function getHelpers()
    {
        return $this->helpers;
    }
    
}
