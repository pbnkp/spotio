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
 * The core Model class. This is the class that all Citrus models in your
 * application should inherit from.
 */
class Model
{
    
    /**
     * The table that this model uses.
     *
     * @access protected
     * @var string
     */
    protected $_table;
    
    
    /**
     * A record of relationships that this model has.
     *
     * @access private
     * @var array
     */
    private $_relationships = array();


    /**
     * Caches the models that are used in the relationships.
     *
     * @access private
     * @var array
     */
    private $_cached_relationship_models = array();
    
    
    /**
     * A record of validations that this model has.
     *
     * @access private
     * @var array
     */
    private $_validations = array(
        'validate_format_of' => array(),
        'validate_length_of' => array(),
        'validate_presence_of' => array(),
        'validate_type_of' => array(),
    );
    
    
    /**
     * The columns that are in this table.
     *
     * @access private
     * @var object
     */
    private $_columns;
    
    
    /**
     * The primary key for this table, of false if there isn't one.
     *
     * @access private
     * @var string|false
     */
    private $_primaryKey;


    /**
     * Are we dealing with a brand new record? If we are then this is set to true.
     *
     * @access private
     * @var bool
     */
    private $_new_record = true;


    /**
     * The record that we're currently processing.
     *
     * @access private
     * @var array
     */
    private $_record;
    
    
    /**
     * If you define your own constructor for your model make sure you call
     * parent::__construct(). You should almost never have to modify the constructor,
     * put all of your code in the protected __setup() method instead.
     *
     * @access public
     */
    public function __construct()
    {
        // Our __setup method contains functions that should be run pre-constructor.
        // Use it to modify table names, validations, relationships etc.
        if (method_exists($this, '__setup')) {
            $this->__setup();
        }
        
        // Get the name of the model that we're implementing. We use this to
        // create our default table name.
        if (!isset($this->_table)) {
            $class = explode('\\', get_class($this));
            $table = array_pop($class);
            $this->_table = \Scarlet\Inflector::underscore($table);
        }
        
        // Enumerate the table so we know which fields exist and what type they are
        list($this->_primaryKey, $this->_columns) = Base::getInstance()->enumerateTable($this->_table);

        $fields = '';
        foreach ($this->_columns as $column => $params) {
            $fields[$column] = '';
        }
        $this->_record = $fields;
    }
    
    
    /**
     * Add a belongs_to relationship to the model. For example, a comment belongs
     * to a user. Should only be called in __setup().
     *
     * @access protected
     * @final
     * @param string $model The model that this model belongs to.
     * @param array $options Any other relationship specific options.
     * @return void
     */
    final protected function belongsTo($model, $options=array())
    {
        // Default options for this type of relationship
        $defaults = array('_type' => 'belongs_to');
        $this->_relationships[$model] = array_merge($defaults, $options);
    }
    
    
    /**
     * Add a has_one relationship to the model. For example, a user has one name.
     * Should only be called in __setup().
     *
     * @access protected
     * @final
     * @param string $model The model that this model has one of.
     * @param array $options Any other relationship specific options.
     * @return void
     */
    final protected function hasOne($model, $options=array())
    {
        // Default options for this type of relationship
        $defaults = array('_type' => 'has_one');
        $this->_relationships[$model] = array_merge($defaults, $options);
    }
    
    
    /**
     * Add a has_many relationship to this model. For example, a user has many
     * emails. Should only be called in __setup().
     *
     * @access protected
     * @final
     * @param string $model The model that this model has many of.
     * @param array $options Any other relationship specific options.
     * @return void
     */
    final protected function hasMany($model, $options=array())
    {
        // Default options for this type of relationship
        $defaults = array('_type' => 'has_many');
        $this->_relationships[$model] = array_merge($defaults, $options);
    }
    
    
    /**
     * Add a has_and_belongs_to_many relationship to this model. For example, an object
     * has many colours and a colour can be on many objects.
     *
     * @access protected
     * @final
     * @param string $mode The model that this has and belongs to many of.
     * @param array $options Any other relationship specific options.
     * @return void
     */
    final protected function hasAndBelongsToMany($model, $options=array())
    {
        // Default options for this type of relationship
        $defaults = array('_type' => 'has_and_belongs_to_many');
        $this->_relationships[$model] = array_merge($defaults, $options);
    }
    
    
    /**
     * Validates the attributes' values by testing whether they match a given
     * regular expression.
     *
     * @access protected
     * @final
     * @param string $attribute The attribute to validate
     * @param string $regex The regular expression to validate against
     * @param string $message An (optional) message to throw when the validation fails
     * @return void
     */
    final protected function validateFormatOf($attribute, $regex, $message=null)
    {
        $this->_validations['validate_format_of'][$attribute] = array('regex' => $regex, 'message' => $message);
    }
    
    
    /**
     * Validates the attribute's values by testing that they are longer/shorter
     * (if the attribute is a string or an array) or greater/less than (if an
     * integer) the required value.
     *
     * You can specify a range or valid values by passing a length in the following
     * format:
     *      X..Y
     *
     * All values greater than or equal to X and less than or equal to Y would be
     * classed as valid.
     *
     * @access protected
     * @final
     * @param string $attribute The attribute to validate
     * @param int|string $length The minimum or maximum size that the attribute can be.
     *                              You can specify a range with double dots e.g.
     *                                  2..5
     *                              would match all values between two and five. If
     *                              you're using ranges then $direction is ignored.
     * @param string $direction Can be either 'eq', 'lt' or 'gt'. Default is 'eq'.
     * @param string $message An (optional) message to throw when the validation fails
     * @return void
     */
    final protected function validateLengthOf($attribute, $length, $direction='eq', $message=null)
    {
        if (strstr($length, '..') !== false) {
            $range = explode('..', $length);
            $this->_validations['validate_length_of'][$attribute] = array(
                'type' => 'range',
                'min' => $range[0],
                'max' => $range[1],
                'message' => $message,
            );
        } else {
            switch ($direction) {
                case 'gt': $type = 'greater_than'; break;
                case 'lt': $type = 'less_than'; break;
                case 'eq': $type = 'equal'; break;
                default: $type = false;
            }
            
            if ($type !== false) {
                $this->_validations['validate_length_of'][$attribute] = array(
                    'type' => $type,
                    'length' => $length,
                    'message' => $message,
                );
            }
        }
    }
    
    
    /**
     *
     */
    final protected function validateTypeOf()
    {
        
    }
    
    
    /**
     * Returns the array or relationships. If 'type' is set then we just return
     * an array of relationships matching that particular type.
     *
     * @access public
     * @final
     * @param string $type The type of relationship to return. Optional
     * @return array
     */
    final public function getRelationships($type=false)
    {
        if ($type === false) return $this->_relationships;
        $type = \Scarlet\Inflector::underscore($type);
        return (isset($this->_relationships[$type])) ? $this->_relationships[$type] : array();
    }
    
    
    /**
     * Returns the array of validations. If 'type' is set then we just return an
     * array of validation matching that particular type.
     *
     * @access public
     * @final
     * @param string $type The type of validation to return. Optional
     * @return array
     */
    final public function getValidations($type=false)
    {
        if ($type === false) return $this->_validations;
        $type = \Scarlet\Inflector::underscore($type);
        return (isset($this->_validations[$type])) ? $this->_validations[$type] : array();
    }


    /**
     * Returns the primary key.
     *
     * @access public
     * @final
     * @return string
     */
    final public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }
    
    
    /**
     * Sets an alternate name to use for the table. Bypasses inflectors, so be
     * careful with your naming.
     *
     * @access public
     * @final
     * @param string $name The name of the table to use for this model
     * @return void
     */
    final public function setTable($name)
    {
        $this->_table = $name;
    }
    
    
    /**
     * Returns the name of the table that we're currently using for this model.
     *
     * @access public
     * @final
     * @return string
     */
    final public function getTable()
    {
        return $this->_table;
    }


    /**
     * Returns the columns of the table that we're currently using for this model.
     *
     * @access public
     * @final
     * @return string
     */
    final public function getColumns()
    {
        return $this->_columns;
    }


    /**
     * Creates a new query object for the model.
     *
     * @access private
     * @final
     * @return object Query
     */
    final public function query()
    {
        return Query::__new($this);
    }


    /**
     * Saves the Model to the database. It doesn't matter whether the model is new
     * or just an update however, it will only be saved if the validations pass.
     *
     * @access public
     * @final
     * @return bool True if the record was saved successfully
     */
    final public function save()
    {
        return $this->query()->save();
    }


    /**
     * Deletes the record from the database.
     *
     * @access public
     * @final
     * @return bool True if the record was deleted successfully
     */
    final public function delete()
    {
        return $this->query()->delete();
    }
    
    
    /**
     * Performs various types of find operations on the model. Direct use of this
     * method is not recommended, instead use one of the magic methods e.g.
     * findById() and findOneById() etc.
     *
     * @access public
     * @final
     * @param string|int $value The value to find.
     * @param string $field The field to do the find on. Defaults to the primary key.
     * @param int $limit The maximum number of records to return. Default to return all.
     * @return mixed
     */
    final private function _find($value, $field=false, $limit=0)
    {
        if ($field === false) $field = $this->_primaryKey;
        if (!array_key_exists($field, $this->_columns)) throw new \Exception("Unknown column '$field'");
        
        $q = $this->query()->select()->where($field, $value);
        if ($limit > 0) $q = $q->limit($limit);

        if ($field === false || $field == $this->_primaryKey || $limit == 1)
            $q = $q->execute(true);

        return $q;
    }
    
    
    /**
     * Catches calls to undefined methods. We use it to provide magic methods such
     * as findById() and findOneById().
     *
     * @access public
     * @final
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    final public function __call($name, $arguments)
    {
        if (strstr($name, 'find') !== false) { // Catch method calls beginning with 'find'
            if (strstr($name, 'findBy') !== false)
                // We're looking for all records that match
                return $this->_find($arguments[0], \Scarlet\Inflector::underscore(str_replace('findBy', '', $name)));
                
            if (strstr($name, 'findOneBy') !== false)
                // We're looking for a single record that matches
                return $this->_find($arguments[0], \Scarlet\Inflector::underscore(str_replace('findOneBy', '', $name)), 1);
                
            // By default, we assume that they're looking for a primary key
            return $this->_find($arguments[0], false, 1);
            
        }

        // This is an unknown method, so create a new Query
        $q = $this->query();
        if (method_exists($q, $name)) {
            return call_user_func_array(array($q, $name), $arguments);
        }

        throw new \Exception("Unknown method '$name'");
    }


    /**
     * We use this to return a record, or relationships, field.
     *
     * @access public
     * @final
     * @param string $name
     * @return mixed
     */
    final public function __get($name)
    {
        // Eventually we'll be checking relationships here

        // Check to see if a field exists, and if it does return it
        if (array_key_exists($name, $this->_record)) {
            return $this->_record[$name];
        }


        // We haven't got a field called that, so fall back to looking for a relationship
        if (array_key_exists($name, $this->_cached_relationship_models)) {
            // Use a cached model as we don't have to worry the database again
            return $this->_cached_relationship_models[$name];

        } elseif (array_key_exists($name, $this->_relationships)) {
            // We haven't got a cached model, so we're going to have to load it
            $relationship = $this->_relationships[$name];

            $table = (array_key_exists('table', $relationship)) ? $relationship['table'] : $name;
            $table = \Scarlet\Inflector::camelize($table, true);

            $class = "\App\Models\\{$table}";
            $m = new $class;
            $o = false;

            switch($relationship['_type']) {
                case 'belongs_to':
                    $key = "{$m->getTable()}_{$m->getPrimaryKey()}";
                    $o = $m->find($this->{$key});
                    break;

                case 'has_one':
                    $table = \Scarlet\Inflector::camelize($this->_table, true);
                    $key = \Scarlet\Inflector::camelize($this->_primaryKey, true);
                    $o = $m->{"findOneBy{$table}{$key}"}($this->{$this->_primaryKey});
                    break;

                case 'has_many':
                    $table = \Scarlet\Inflector::camelize($this->_table, true);
                    $key = \Scarlet\Inflector::camelize($this->_primaryKey, true);
                    $o = $m->{"findBy{$table}{$key}"}($this->{$this->_primaryKey});
                    break;

                default:
                    throw new \Exception("Unknown relationship type '{$relationship['_type']}'");
            }

            $this->_cached_relationship_models[$name] = $o;
            return $o;
        }

        // This is an unknown field or relationship
        throw new \Exception("Unknown column '$name' on table '{$this->_table}'");
    }


    /**
     * We use this magic method to hijack the writing of record fields. This means
     * that we can allow the developer to write instance methods that modify the
     * data being written. For example:
     *
     *      public function password_()
     *
     * Would hijack the writing of the password so we can do encryption etc. Make
     * sure that you return a value, otherwise your field will be permanently set
     * to 'null'!
     *
     * These methods are automatically called by Citrus, you don't have to do
     * anything else but just add this method to your model.
     *
     * @access public
     * @final
     * @param string $name
     * @param mixed $value
     * @return void
     */
    final public function __set($name, $value)
    {
        if (method_exists($this, "{$name}_")) {
            $value = call_user_func_array(array($this, "{$name}_"), array($value));
        }

        $this->_record[$name] = $value;
    }


    /**
     * Sets the record that the hydrated model is now working with.
     *
     * @access public
     * @final
     * @param object $record
     * @return false
     */
    final public function setRecord($record=false)
    {
        $fields = array();

        // Parse the returned record
        if ($record !== false) {
            foreach ($record as $k => $v) {
                list($table, $column) = explode('__', $k);
                $fields[$column] = $v;
            }
        }

        $this->_record = $fields;
        $this->_new_record = false;
    }


    /**
     * Returns the record's values in a great big chunk.
     *
     * @access public
     * @final
     * @return array
     */
    final public function toArray()
    {
        return $this->_record;
    }


    /**
     * Is this record new or not?
     *
     * @access public
     * @final
     * @return bool
     */
     final public function _isNew()
     {
         return $this->_new_record;
     }
    
}
