<?php
Citrus\Base::config(function($db){
   /**
    * You will need the PHP PDO extensions for Citrus ORM to work. The PDO extension
    * supports MS SQL, Firebird, IBM, Informix, MySQL, Oracle, ODBC, DB2, PostgreSQL,
    * SQLite and 4D.
    *
    * We recommend that you use MySQL 5.0 or greater as your database.
    */
   
   $db->addConnection('development', array(
       'adapter' => 'mysql',
       'encoding' => 'UTF-8',
       'database' => 'scarlet_development',
       'username' => 'root',
       'password' => 'root',
       'host' => 'localhost',
   ));


   // Warning: The database defined as "test" will be erased and re-generated from
   // your development database. Do not set this db to the same as development or production.
   /*$db->addConnection('test', array(
       'adapter' => 'mysql',
       'encoding' => 'UTF-8',
       'database' => 'your_app_test',
       'username' => 'root',
       'password' => 'root',
       'host' => 'localhost',
   ));*/

});
