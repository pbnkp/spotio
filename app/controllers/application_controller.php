<?php
namespace App\Controllers;
use Scarlet\Controller as Controller;
/**
 * This is the controller that all your other controllers should inherit from.
 * Add methods here that you want to be available in all your other controllers.
 */
class ApplicationController extends Controller
{
    
    protected function runApplescript($script)
    {
      exec("osascript " . ROOT . DS . "lib" . DS . "applescripts" . DS . $script . ".applescript");
      exit;
    }
    
}
