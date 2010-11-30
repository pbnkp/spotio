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
 * Handles the rendering of layouts, views and partials.
 */
class View
{
    
    protected $_content = array(
        '__main__' => '',
    );
    
    
    protected $content;
    
    
    protected $Controller;
    protected $view_folder;
    protected $format;
    
    
    protected $data;
    protected $params;


    private $helperMethods = array();
    private $helpers = array();
    
    
    /**
     * Initialises a new view whether it's a layout, view or partial. Don't call
     * this in your apps, use the provided helper functions instead.
     * 
     * @access public
     * @param object $Controller The controller object
     * @param string $view The view file to render
     * @param string $format The format that we're rendering
     * @param string $type The type of file we're rendering. Can be either 'view',
     *                      'layout' or 'partial'. Defaults to 'view'.
     * @return object
     */
    public function __construct($Controller, $view, $format, $type = 'view', $content = '')
    {
        $this->Controller =& $Controller;
        $this->data =& $this->Controller->data;
        $this->params =& $this->Controller->params;
        $this->format = $format;
        
        $type = strtolower($type);
        switch ($type) {
            case 'view':
                list($controller, $action) = explode('/', $view);
                $this->view_folder = $controller;
                $viewfile = VIEWS . DS . $controller . DS . $action;
                break;
            
            case 'layout':
                $this->view_folder = 'layouts';
                $viewfile = LAYOUTS . DS . $view;
                break;
            
            case 'partial':
                list($controller, $action) = explode('/', $view);
                $viewfile = VIEWS . DS . $controller . DS . '_' . $action;
                $this->data = $content;
                break;
        }
        
        $viewfile .= ".{$this->format}.php";
        
        if (!file_exists($viewfile)) {
            // We can't find the viewfile, so if we're in debug mode throw an exception
            if (Environment::getInstance()->isDebug()) {
                $prefix = ($type == 'partial') ? '_' : '';
                $a = (isset($action)) ? $action : $view;
                throw new \Exception("Missing template {$this->view_folder}/$prefix$a.{$this->format}.php");
            } else {
                // If this is a view or layout then go 404
                if ($type != 'partial') {
                    echo file_get_contents(PUBLIC_DIR . DS . '404.html');
                    exit;
                }
            }
        }
        
        // If we've got this far then everything's good, so render the viewfile.
        if ($type != 'partial') {
            if (is_array($content)) {
                $this->_content = $content;
            } else {
                $this->_content['__main__'] = $content;
            }
            if (!isset($this->_content['__main__'])) $this->_content['__main__'] = '';
            $this->content =& $this->_content['__main__'];
        }
        
        $this->loadHelpers();
        
        ob_start();
        extract($this->data);
        include($viewfile);
        $this->_content['__main__'] = ob_get_contents();
        ob_end_clean();
    }


    /**
     * Loads the helpers that we are using in the view. Using PHP's reflection we
     * can load the methods without needing to load the class. We then instantiate
     * the class only when someone calls one of it's methods.
     *
     * @access private
     * @return void
     */
    private function loadHelpers()
    {
        $helpers = $this->Controller->getHelpers();
        foreach ($helpers as $helper) {
            $class = new \ReflectionClass($helper);
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $this->helperMethods[$method->name] = $method->class;
            }
        }
    }


    /**
     * Catches an undefined method call, usually belonging to a helper. We then
     * search for a class that can help us, instantiate it and return the method
     * back to the view.
     *
     * @access public
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->helperMethods)) {
            $class = $this->helperMethods[$name];
            if (!array_key_exists($class, $this->helpers)) {
                // We haven't created this helper before, so create it
                $this->helpers[$class] = new $class;
            }

            return call_user_func_array(array($this->helpers[$class], $name), $arguments);
        }

        throw new \Exception("Unkown method '$name'");
    }
    
    
    /**
     * Rendered content. If |$return_all| is set to |true| then all content,
     * including |content_for| values, is returned. Otherwise just the rendered
     * view is returned.
     * 
     * @access public
     * @param bool $return_all Do not set this if you are echoing a view or partial
     *                          content in a parent view
     * @return mixed
     */
    public function content($return_all=false)
    {
        if ($return_all) return $this->_content;
        return $this->_content['__main__'];
    }
    
    
    /**
     * A Rails inspired content_for view helper. Set the second parameter to store
     * content for later use, leave the second parameter (or set it to false) to
     * retrieve that content.
     * 
     * @access public
     * @param string $key The key to store this content under
     * @param mixed $value The content. Leave this unset if you are retrieving data
     * @return mixed
     */
    public function content_for($key, $value=false)
    {
        if ($value === false) {
            if (array_key_exists($key, $this->_content)) return $this->_content[$key];
            return false;
        }
        
        $this->_content[$key] = $value;
    }
    
    
    /**
     * Renders a partial. You can pass parameters to the partial through the
     * second parameter.
     * 
     * @access public
     * @param string $partial The name of the partial to load
     * @param array $data The data to pass to the partial (optional)
     * @return string The rendered partial
     */
    public function partial($partial, $data = array())
    {
        if (strpos($partial, '/') === false) {
            $partial = $this->view_folder . '/' . $partial;
        }
        
        $View = new View($this->Controller, $partial, $this->format, 'partial', $data);
        return $View->content();
    }
    
}
