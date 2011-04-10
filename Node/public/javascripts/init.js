(function() {
  var current_status, socket;
  current_status = {};
  socket = new io.Socket(window.location.hostname, {
    port: 8081
  });
  socket.connect();
  socket.on('message', function(data) {
    current_status = JSON.parse(data);
    document.getElementById("track").innerHTML = current_status["track"];
    return document.getElementById("artist").innerHTML = current_status["artist"];
  });
  document.getElementById("previous-track").addEventListener('click', function() {
    return socket.send('previous-track');
  });
  document.getElementById("play-pause-track").addEventListener('click', function() {
    return socket.send('play-pause-track');
  });
  document.getElementById("next-track").addEventListener('click', function() {
    return socket.send('next-track');
  });
}).call(this);
