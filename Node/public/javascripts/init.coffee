current_status = {}


socket = new io.Socket 'localhost', {
  port: 8081
};

socket.connect()

socket.on 'message', (data) ->
  current_status = JSON.parse(data)
  document.getElementById("now-playing").innerHTML = current_status["track"]


# Register eventHandlers for the Spotio controls
document.getElementById("previous-track").addEventListener 'click', ->
  socket.send 'previous-track'


document.getElementById("play-pause-track").addEventListener 'click', ->
  socket.send 'play-pause-track'


document.getElementById("next-track").addEventListener 'click', ->
  socket.send 'next-track'