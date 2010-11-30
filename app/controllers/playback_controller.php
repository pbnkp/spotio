<?php
namespace App\Controllers;
class PlaybackController extends ApplicationController
{
  
  public function play_pause()
  {
    $this->runApplescript("play_pause");
  }
  
  
  public function previous_track()
  {
    $this->runApplescript("previous_track");
  }
  
  
  public function next_track()
  {
    $this->runApplescript("next_track");
  }
  
}
