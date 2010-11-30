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
 *
 * It implements the Iterator interface so it's possible to iterate through the
 * returned records.
 */
class Query extends Iterator
{
    
    /**
     * The model that is creating this query.
     * 
     * @access private
     * @var object Model
     */
    private $_Model = false;
    
    
    /**
     * Keeps a record of paramaters associated with this query e.g. WHERE conditions, etc.
     * 
     * @access private
     * @var array
     **/
    private $_params = array(
        'action' => false,
        'limit' => false,
        'offset' => false,
        'select' => array(),
        'where' => array(),
    );


    /**
     * Have we executed the query?
     *
     * @access private
     * @var bool
     */
    private $_executed = false;


    /**
     * Keeps a record of the query resource so we can lazy load records in.
     *
     * @access private
     * @var resource
     */
    private $_query;


    /**
     * Keeps a record of the returned results.
     *
     * @access private
     * @var array
     */
    protected $_records = array();
    
    
    /**
     * The constructor.
     *
     * @access public
     * @param string $Model The model creating this query
     */
    public function __construct($Model=false)
    {
        $this->_Model = $Model;
        $this->_iterable =& $this->_records;
    }
    
    
    /**
     * An alternative to 'new Query()' so that we can support chaining.
     *
     * @access public
     * @static
     * @param object $Model The model creating this query
     * @return object $this
     */
    public static function __new($Model=false)
    {
        return new Query($Model);
    }


    /**
     * We define our own valid() method here for iterator, so we can check to see
     * if execute() has been called yet. If it hasn't, then do it and continue.
     *
     * @access public
     * @return bool
     */
    public function valid()
    {
        if ($this->_executed === false) $this->execute();
        $p = parent::valid();

        if ($p === false) {
            // The iterator thinks that this record doesn't exist. However, it
            // may just not have been loaded yet. We need to check that here.
            $record = $this->_query->fetch(\PDO::FETCH_OBJ);
            if ($record === false) return false;

            $this->_records[] = Record::hydrate($this->_Model, $record);
            return true;
        }

        return $p;
    }
    
    
    /**
     * Performs a simple SQL query, bypassing all ORM features. However, that
     * doesn't mean that it isn't actually used by the ORM.
     *
     * @access public
     * @param string $sql The SQL to perform
     * @param bool $autofetch Fetch the results immediately?
     * @return mixed
     */
    public function sql($sql, $all=false)
    {
        $q = Base::connectionManager()->getConnection()->query($sql);
        return ($all) ? $q->fetchAll() : $q;
    }


    /**
     * Executes the query.
     *
     * @access public
     * @param bool $batchmode Whether to return all results at once rather than
     *                          the default method of lazy loading.
     * @return object $this
     */
    public function execute($batchmode=false)
    {
        // Build the SQL to execute, run it, and set the $_query resource to the
        // cached PDO query.
        $sql = $this->_buildSQL();
        $this->_query = $this->sql($sql);
        $this->_executed = true;

        // Return all records immediately, if required. Otherwise we fall back
        // to the default method of lazy loading the results.
        if ($batchmode === true || $this->_params['limit'] == 1) {
            while ($record = $this->_query->fetch(\PDO::FETCH_OBJ))
                $this->_records[] = Record::hydrate($this->_Model, $record);

            if (empty($this->_records)) {
                return false;
            } elseif (count($this->_records) == 1) {
                return $this->_records[0];
            }
        }

        return $this;
    }


    /**
     * Saves a record to the database.
     *
     * @access public
     * @return bool|PDOStatement Returns false if the save failed
     */
    public function save()
    {
        $params = $this->_Model->toArray();

        if ($this->_Model->_isNew()) {
            // This is a new record, so insert it into the database
            $columns = array();
            $values = array();

            foreach ($params as $c => $v) {
                $columns[] = "`$c`";
                $values[] = "'$v'";
            }
            
            $columns = implode(',', $columns);
            $values = implode(',', $values);
            
            $sql = "INSERT INTO `{$this->_Model->getTable()}` ({$columns}) VALUES ({$values})";
            return $this->sql($sql);

        } else {
            // This is an existing record, so just update it in the database
            $updates = array();
            foreach ($params as $c => $v) {
                $updates[] = "{$c} = '{$v}'";
            }
            $updates = implode(',', $updates);

            $pk = $this->_Model->getPrimaryKey();
            $pkv = $this->_Model->{$pk};

            $sql = "UPDATE `{$this->_Model->getTable()}` SET {$updates} WHERE {$pk} = '{$pkv}'";
            return $this->sql($sql);
            
        }
    }


    /**
     * Deletes a single record from the database.
     *
     * @access public
     * @return bool
     */
    public function delete()
    {
        if ($this->_Model->_isNew())
           return false;

        $pk = $this->_Model->getPrimaryKey();
        $pkv = $this->_Model->{$pk};
        $sql = "DELETE FROM `{$this->_Model->getTable()}` WHERE {$pk} = '{$pkv}'";
        return $this->sql($sql);
    }


    /**
     * Deletes all instances of records that match the query. Useful as a shortcut
     * when doing findMany and then deleting them.
     *
     * @access public
     * @return void
     */
    public function deleteAll()
    {
        $records = $this->execute();
        foreach ($records as $record)
            $record->delete();
    }


    /**
     * Builds the actual SQL for the query.
     *
     * @access private
     * @return string
     */
    private function _buildSQL()
    {
        switch(strtolower($this->_params['action'])) {
            case 'select':
                return $this->_buildSelect();
                break;
        }
    }


    /**
     * Builds the SELECT part of the query.
     *
     * @access private
     * @return string
     */
    private function _buildSelect()
    {
        $columns = implode(', ', $this->_params['select']);

        $where = '';
        $f = true;
        foreach ($this->_params['where'] as $cond) {
            if ($f === false) $where .= " {$cond['type']}";
            else $where .= ' WHERE';

            $where .= " {$cond['column']} {$cond['operator']} '{$cond['value']}'";
        }

        $order = '';
        $limit = ($this->_params['limit'] === false) ? null : " LIMIT {$this->_params['limit']}";
        $offset = ($this->_params['offset'] === false) ? null : " OFFSET {$this->_params['offset']}";

        return "SELECT {$columns} FROM `{$this->_Model->getTable()}`{$where}{$order}{$limit}{$offset}";
    }
    
    
    /**
     * Starts a new transaction. Nothing is written to your database until you
     * use commit(). If something has gone wrong, then you can use the rollback()
     * method to undo your changes.
     * 
     * @access public
     * @final
     * @return mixed
     */
    final public function startTransaction()
    {
        return Base::connectionManager()->getConnection()->beginTransaction();
    }
    
    
    /**
     * Commits the changes to the database.
     * 
     * @access public
     * @final
     * @return mixed
     */
    final public function commit()
    {
        return Base::connectionManager()->getConnection()->commit();
    }
    
    
    /**
     * Rollbacks the latest transaction.
     * 
     * @access public
     * @final
     * @return mixed
     */
    final public function rollback()
    {
        return Base::connectionManager()->getConnection()->rollBack();
    }
    


    /**
     * Choose which columns to return from the database. There is no need to add
     * the SELECT statement, this will be automatically by the ORM. You can only
     * call this method once per query. Calling this multiple times will simply
     * overwrite the previous SELECT statment.
     *
     * Usage:
     *      ->select()      // Returns all fields by default
     *      ->select(column_1, column_2, ... column_3)
     *
     * @access public
     * @final
     * @params string The columns to return. By default will return all columns
     * @return object $this
     */
    final public function select()
    {
        $this->_params['action'] = 'select';
        $columns = func_get_args();

        if (empty($columns)) {
            // We haven't defined a particular column, so load them all.
            $this->_params['select'][] = $this->_formatColumn('*');
        } else {
            foreach ($columns as $column) {
                $this->_params['select'][] = $this->_formatColumn($column);
            }
        }
        
        return $this;
    }
    
    
    /**
     * Describes a table, mapping the returned columns into some sane defaults
     * that Citrus can then understand.
     *
     * @access public
     * @param string $table
     * @return array
     */
    public function describe($table)
    {
        $q = $this->sql("DESCRIBE `$table`", true);
        
        $primary = '';
        $columns = array();
        
        foreach ($q as $r) {
            $columns[$r['Field']] = array(
                'type' => $r['Type'],
                'null' => (strtolower($r['Null']) == 'no') ? false : true,
                'key' => (empty($r['Key'])) ? false : $r['Key'],
                'default' => $r['Default'],
            );
            
            if ($r['Key'] == 'PRI') $primary = $r['Field'];
        }
        
        return array($primary, $columns);
    }
    
    
    /**
     * Adds conditions to the SQL statement. By default we perform a "WHERE X=Y"
     * however, you can also perform:
     *      =, !=, >, <, like, between, is
     * 
     * Conditions can be set by:
     *      ->where("$your_column", "$value")           : matches $your_column = $value
     *      ->where("$your_column", "$operator $value") : matches $your_column $operator $value
     * 
     * In addition, you can also specify whether you are doing an "AND" or an
     * "OR" condition.
     *
     * @access  public
     * @param string $column The name of the column to perform the WHERE on
     * @param string $value The value of the column to match.
     * @param string $type Either an AND or OR. Defaults to AND.
     * @return  object $this For chaining
     */
    public function where($column, $value, $type='AND')
    {
        $operators = array('=', '!=', '>', '<', 'like', 'between', 'is');
        
        $operator = explode(' ', $value);
        $operator = strtolower($operator[0]);
        if (!in_array($operator, $operators)) {
            $operator = '=';
        } else {
            $value = explode(' ', $value);
            $operator = $value[0];
            unset($value[0]);
            $value = implode(' ', $value);
        }
        
        $this->_params['where'][] = array(
            'column' => "{$this->_Model->getTable()}.{$column}",
            'operator' => $operator,
            'value' => $value,
            'type' => $type,
        );
        
        return $this;
    }
    
    
    /**
     * Convenience method for where('column', 'value', 'AND')
     *
     * @access public
     * @final
     * @param string $columnName The name of the column to perform the WHERE statement on
     * @param string $value The value of the column to look up. By default this is
     *                          a = where, however you can prepend any operator to this
     *                          value (such as >, < or LIKE)
     * @return object $this
     */
    final public function andWhere($columnName, $value)
    {
        return $this->where($columnName, $value, 'AND');
    }
    
    
    /**
     * Convenience method for where('column', 'value', 'OR')
     *
     * @access public
     * @final
     * @param string $columnName The name of the column to perform the WHERE statement on
     * @param string $value The value of the column to look up. By default this is
     *                          a = where, however you can prepend any operator to this
     *                          value (such as >, < or LIKE)
     * @return object $this
     */
    final public function orWhere($columnName, $value)
    {
        return $this->where($columnName, $value, 'OR');
    }
    
    
    /**
     * Performs a SQL LIMIT. The LIMIT statement will be automatically added to the
     * query, simply state by how many results you want. You can only call this once
     * per query. Calling this multiple times will simply overwrite the previous
     * LIMIT statement.
     *
     * @access public
     * @final
     * @param int $limit The maximum number of results to return.
     * @return object $this
     */
    final public function limit($limit)
    {
        $this->_params['limit'] = $limit;
        return $this;
    }
    
    
    /**
     * Performs a SQL offset. The OFFSET statement will be automatically added to the
     * query, simply state by how much you want the results to be offset. You can
     * only call this once per query. Calling this multiple times will simply overwrite
     * the previous OFFSET statement.
     *
     * @access public
     * @final
     * @param int $offset The result set offset
     * @return object $this
     */
    final public function offset($offset)
    {
        $this->_params['offset'] = $offset;
        return $this;
    }


    /**
     * Converts a column name into something that the ORM can understand. This is
     * essential to making the relationships work properly.
     *
     * @access private
     * @param string $column The column name to format
     * @return string
     */
    private function _formatColumn($column)
    {
        $table = $this->_Model->getTable();

        if ($column == '*') {
            // We are converting every column in the table
            $columns = $this->_Model->getColumns();
            $str = array();
            foreach ($columns as $column => $params) {
                $str[] = "{$table}.{$column} as {$table}__{$column}";
            }
            return implode(', ', $str);
        }

        return "{$table}.{$column} as {$table}__{$column}";
    }


    /**
     * Catches an unknown method calls. We are assuming that any methods not defined
     * here are actually part of the PDO, so we try to map to that instead.
     *
     * @access private
     * @final
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    final public function __call($name, $arguments)
    {
        if (!$this->_executed) $this->execute();
        return call_user_func_array(array($this->_query, $name), $arguments);
    }
    
}
