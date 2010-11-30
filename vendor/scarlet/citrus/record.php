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
 * All Citrus database records are an instance of this class. It allows us to
 * perform CRUD easily.
 */
class Record
{

    /**
     * Turns a record object into a hydrate model. This is important so that we
     * can add instance methods to models.
     */
    public static function hydrate(Model $Model, $record=false)
    {
        $Model->setRecord($record);
        return $Model;
    }

}
