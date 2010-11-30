<?php

namespace Scarlet;

class Router
{
    
    private static $_instance;
    private $_default_controller = 'error';
    private $_default_action = 'index';
    
    
    public $request_uri;
    public $routes;
    private $controller, $controller_name;
    private $action, $id;
    private $params;
    private $format;
    public $route_found = false;
    
    
    private function __construct()
    {
        $request = $_SERVER['REQUEST_URI'];
        $pos = strpos($request, '?');
        if ($pos) $request = substr($request, 0, $pos);
        
        $this->request_uri = $request;
        $this->routes = array();
        $this->default_routes();
    }
    
    
    public static function getInstance()
    {
        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        
        self::$_instance = new self();
        return self::$_instance;
    }
    
    
    public static function draw($lambda)
    {
        $lambda(self::getInstance());
    }
    
    
    public function getController($normalised = true)
    {
        if (!isset($this->controller)) return false;

        if ($normalised) return 'App\Controllers\\' . Inflector::camelize($this->controller_name);
        return $this->controller;
    }
    
    
    public function getAction()
    {
        return Inflector::underscore($this->action);
    }
    
    
    public function getParams()
    {
        return $this->params;
    }


    public function getFormat()
    {
        return $this->format;
    }
    
    
    public function root($target)
    {
        $target = $this->parse_target($target);
        $this->_default_controller = $target['controller'];
        $this->_default_action = $target['action'];
        $this->match('/', $target);
    }
    
    
    public function match($rule, $target=array(), $conditions=array())
    {
        $target = $this->parse_target($target);
        $this->routes[$rule] = new Route($rule, $this->request_uri, $target, $conditions);
    }
    
    
    public function default_routes()
    {
        $this->root('home#index');
    }
    
    
    private function parse_target($target)
    {
        if (is_string($target)) {
            list($controller, $action) = explode('#', $target);
            $target = array('controller' => $controller, 'action' => $action);
        }
        
        return $target;
    }
    
    
    private function set_route($route)
    {
        $this->route_found = true;
        $params = $route->params;
        $this->controller = $params['controller']; unset($params['controller']);
        $this->action = (isset($params['action'])) ? $params['action'] : null; unset($params['action']);
        $this->id = (isset($params['id'])) ? $params['id'] : false;
        $this->format = $route->format;
        $this->params = array_merge($params, $_GET);
        
        if (empty($this->controller)) $this->controller = $this->_default_controller;
        if (empty($this->action)) $this->action = $this->_default_action;
        if (empty($this->id)) $this->id = null;

        $this->controller_name = $this->controller . '_controller';
    }
    
    
    public function execute()
    {
        foreach($this->routes as $route) {
            if ($route->is_matched) {
                $this->set_route($route);
                break;
            }
        }
    }
    
}
 
class Route {
    public $is_matched = false;
    public $params;
    public $format;
    public $url;
    private $conditions;
    private $url_regex;

    function __construct($url, $request_uri, $target, $conditions)
    {
        $this->url = $url;
        $this->params = array();
        $this->conditions = $conditions;
        $p_names = array(); $p_values = array();

        $url = str_replace(array('(',')'), array('(', ')?'), $url);
        
        preg_match_all('@:([\w]+)@', $url, $p_names, PREG_PATTERN_ORDER);
        $p_names = $p_names[0];
        
        $url_regex = preg_replace_callback('@:[\w]+@', array($this, 'regex_url'), $url);
        $url_regex .= '/?';
        $this->url_regex = $url_regex;

        if (preg_match('/(.*)\.([a-z]+)$/i', $request_uri, $match)) {
            $request_uri = $match[1];
            $this->format = $match[2];
        } else {
            $this->format = 'html';
        }

        if (preg_match('@^' . $url_regex . '$@', $request_uri, $p_values)) {
            array_shift($p_values);

            // Removes any poorly formatted url fragments left over from our
            // nested optional routes.
            foreach ($p_values as $i => $v)
                if (strstr($v, '/') !== false) unset($p_values[$i]);
                
            $p_values = array_values($p_values);

            foreach($p_names as $index => $value)
                $this->params[substr($value,1)] = (isset($p_values[$index])) ? urldecode($p_values[$index]) : false;;
            
            foreach($target as $key => $value)
                $this->params[$key] = str_replace('/', '', $value);
            
            $this->is_matched = true;
        }
        
        unset($p_names); unset($p_values);
    }
    
    
    function regex_url($matches)
    {
        $key = str_replace(':', '', $matches[0]);
        
        if (array_key_exists($key, $this->conditions)) {
            return '('.$this->conditions[$key].')';
        } else {
            return '([a-zA-Z0-9_-]+)';
        }
    }
}
