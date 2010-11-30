var Spotio = function(options){
  options = $.extend({
    play_pause_trigger: "#play_pause",
    previous_track_trigger: "#previous_track",
    next_track_trigger: "#next_track",
    volume_range: "#volume"
  }, options);
  
  
  var _url = {
    play_pause_track: "/playback/play_pause",
    previous_track: "/playback/previous_track",
    next_track: "/playback/next_track"
  };
  
  
  // These are the core Spotio methods. Feel free to call them in plugins or your own code.
  var _m = {
    play: {
      toggle_pause: function(){
        $.post(_url.play_pause_track);
      },
      
      previous_track: function(){
        $.post(_url.previous_track);
      },
      
      next_track: function(){
        $.post(_url.next_track);
      }
    }
  };
  
  
  // Initialise the websocket
  var host = "localhost";
  var port = 8080;
  
  var socket = new io.Socket(host, {port: port});
  socket.connect();
  
  
  // Initialise triggers
  $(options.play_pause_trigger).click(_m.play.toggle_pause);
  $(options.previous_track_trigger).click(_m.play.previous_track);
  $(options.next_track_trigger).click(_m.play.next_track);
  
  
  return _m;
};