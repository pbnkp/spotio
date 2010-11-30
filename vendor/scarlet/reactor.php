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
 * Handles all of our evented code. We don't define any default events in this
 * class, they are created dynamically elsewhere in the code. This means it's
 * nice and easy for plugins to create, and hook into, custom events.
 * 
 * You can assign lambda functions, static class methods, instance methods or
 * just a plain old function as event hooks.
 * 
 * Some events pass a number of arguments to the callback functions and, in some
 * cases, expect a return value. For a reasonably complete list of internal events
 * and how to author Scarlet plugins see http://scarlet-framework.com/docs
 */
class Reactor
{
    
    /**
     * Keeps a record of all registered events and their hooks.
     * 
     * @access private
     * @static
     * @var array
     */
    private static $_events = array();
    
    
    /**
     * Keeps a record of the order that each event callback should be executed 
     * should be executed in.
     * 
     * @access private
     * @static
     * @var array
     */
    private static $_event_priorities = array();
    
    
    /**
     * The different types of callback we can handle.
     */
    const CALLBACK_TYPE_LAMBDA = 1;
    const CALLBACK_TYPE_INSTANCE_METHOD = 2;
    const CALLBACK_TYPE_STATIC_METHOD = 3;
    
    
    /**
     * This class is a singleton.
     * 
     * @access private
     */
    private function __construct(){}
    
    
    /**
     * Registers a callback/hook to an event. There is no need to create/define
     * the event before registering the hook.
     * 
     * You can define callbacks in three ways:
     *      - as a lambda function
     *      - as a string
     *      - as an array
     * 
     * Callbacks defined as strings will be treated as a function or static
     * method. If you go down this route make sure that you have fully namespaced
     * your methods. Callbacks defined as an array allow you to use an object
     * instance method.
     * 
     * See http://scarlet-framework.com/docs for more information.
     * 
     * @access public
     * @static
     * @param string $event The name of the event that you want to hook into.
     * @param string|function|array $callback The callback function. Should be properly namespaced.
     * @param int $priority The priority of this callback. Higher priorities get
     *                      executed first. Default 50.
     * @return mixed The unique hook identifier
     */
    public static function bind($event, $callback, $priority=50)
    {
        switch (true) {
            case is_array($callback):
                $serialized_callback = '\\' . get_class($callback[0]) . '->' . $callback[1];
                $type = self::CALLBACK_TYPE_INSTANCE_METHOD;
                break;
            
            case is_object($callback) && is_callable($callback):
                $serialized_callback = uniqid();
                $type = self::CALLBACK_TYPE_LAMBDA;
                break;
            
            case is_string($callback):
                $serialized_callback = $callback;
                $type = self::CALLBACK_TYPE_STATIC_METHOD;
                break;
            
            default:
                throw new \Exception("Unexpected callback type");
                break;
        }
        
        
        // Create the event if it doesn't already exist
        if (!array_key_exists($event, self::$_events))
            self::$_events[$event] = array();
        
        if (!array_key_exists($event, self::$_event_priorities))
            self::$_event_priorities[$event] = array();
        
        
        // And finally, attach the hook to the event
        self::$_events[$event][$serialized_callback] = array('callback' => $callback, 'type' => $type);
        self::$_event_priorities[$event][$serialized_callback] = $priority;
        
        return $serialized_callback;
    }
    
    
    /**
     * Fires an event. Will execute every method hooked into the event in the
     * order of priority. Will take an undefined number of arguments where the
     * first is always the name of the event and the rest are the arguments to
     * pass to the callback functions.
     * 
     * @access public
     * @static
     * @param string $event The name of the event to fire
     * @params mixed The arguments to pass to the callback functions
     * @return bool
     */
    public static function fire()
    {
        $args = func_get_args();
        if (count($args) < 1) throw new \Exception("Event to fire is not specified");
        $event = array_shift($args);
        
        // Run the event callbacks, and sort them by priority. If the event doesn't
        // exist then just return false.
        if (!array_key_exists($event, self::$_events)) return false;
        
        $callbacks = self::$_event_priorities[$event];
        arsort($callbacks);
        
        foreach ($callbacks as $callback => $priority) {
            $callback = self::$_events[$event][$callback];
            
            switch ($callback['type']) {
                case self::CALLBACK_TYPE_LAMBDA:
                    $lambda = $callback['callback'];
                    $lambda($args);
                    break;
                
                case self::CALLBACK_TYPE_INSTANCE_METHOD:
                    
                    break;
                
                case self::CALLBACK_TYPE_STATIC_METHOD:
                    
                    break;
            }
        }
    }
    
}
