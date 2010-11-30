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
 * Provides instant iterability to classes. You just have to make sure that the
 * variables that you want to iterate through are defined in the protected $_iterable
 * array.
 */
class Iterator implements \Iterator
{

    /**
     * All your variables that you want to iterate through should go here.
     *
     * @access protected
     * @var array
     */
    protected $_iterable = array();


    /**
     * Part of the Iterator. Keeps track of the result that we're currently on.
     *
     * @access private
     * @var int
     */
    private $index = 0;


    /**
     * Part of the Iterator spec.
     *
     * @access public
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }


    /**
     * Part of the Iterator spec.
     *
     * @access public
     * @return object Record
     */
    public function current()
    {
        $k = array_keys($this->_iterable);
        return $this->_iterable[$k[$this->index]];
    }


    /**
     * Part of the Iterator spec.
     *
     * @access public
     * @return object|false
     */
    public function next()
    {
        $k = array_keys($this->_iterable);
        $this->index++;

        if (!array_key_exists($this->index, $k))
            return false;

        if (array_key_exists($k[$this->index], $this->_iterable))
            return $this->_iterable[$k[$this->index]];

        return false;

    }


    /**
     * Part of the Iterator spec.
     *
     * @access public
     * @return object
     */
    public function key()
    {
        $k = array_keys($this->_iterable);
        return $k[$this->index];
    }


    /**
     * Part of the Iterator spec.
     *
     * @access public
     * @return bool
     */
    public function valid()
    {
        $k = array_keys($this->_iterable);
        return isset($k[$this->index]);
    }

}