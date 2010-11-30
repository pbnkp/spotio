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


require_once SCARLET . DS . 'autoloader.php';
require_once SCARLET . DS . 'inflector.php';

// Citrus is the Scarlet ORM. We just need to register the autoloader here and
// Scarlet takes care of the rest.
Autoloader::add('citrus', CITRUS . DS . '{name}');


$Environment = Environment::getInstance();


$Cli = Cli::getInstance($Environment);
$Cli->handle();
