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
 * Use the DS to separate the directories in other defines. Also a useful shortcut.
 */
if (!defined('DS'))             define('DS', DIRECTORY_SEPARATOR);

/**
 * These defines should only be edited if you have installed Scarlet with a
 * non-default directory structure. When using custom settings be sure to use
 * the DS and do not add a trailing DS.
 */
if (!defined('ROOT'))           define('ROOT', dirname(dirname(__FILE__)));
if (!defined('APP'))            define('APP', ROOT . DS . 'app');
if (!defined('CONTROLLERS'))    define('CONTROLLERS', APP . DS . 'controllers');
if (!defined('MODELS'))         define('MODELS', APP . DS . 'models');
if (!defined('VIEWS'))          define('VIEWS', APP . DS . 'views');
if (!defined('LAYOUTS'))        define('LAYOUTS', VIEWS . DS . 'layouts');
if (!defined('CONFIG'))         define('CONFIG', ROOT . DS . 'config');
if (!defined('DB_DIR'))         define('DB_DIR', ROOT . DS . 'db');
if (!defined('PUBLIC_DIR'))     define('PUBLIC_DIR', ROOT . DS . 'public');
if (!defined('TEST_DIR'))       define('TEST_DIR', ROOT . DS . 'test');
if (!defined('VENDOR'))         define('VENDOR', ROOT . DS . 'vendor');
if (!defined('SCARLET'))        define('SCARLET', VENDOR . DS . 'scarlet');
if (!defined('CITRUS'))         define('CITRUS', SCARLET . DS . 'citrus');
if (!defined('PLUGINS'))        define('PLUGINS', VENDOR . DS . 'plugins');


require_once SCARLET . DS . 'autoloader.php';
require_once SCARLET . DS . 'inflector.php';

// Citrus is the Scarlet ORM. We just need to register the autoloader here and
// Scarlet takes care of the rest.
Autoloader::add('citrus', CITRUS . DS . '{name}');


$Environment = Environment::getInstance();


$Kernel = new Kernel($Environment);
$Kernel->handle();
