<?php
namespace Scarlet;

Cli::task("about", "Show details of your PHP and Scarlet environment", function(){
    $php_version = phpversion();
    $scarlet_version = file_get_contents(SCARLET . DS . "VERSION");
    $app_root = ROOT;
    $environment = \Scarlet\Environment::getInstance()->getMode(true);
    
    echo <<<OUTPUT
About your application's environment
PHP version         {$php_version}
Scarlet version     {$scarlet_version}
Application root    {$app_root}
Environment         {$environment}
OUTPUT;
});
