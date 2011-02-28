http = require('http')
io = require('./vendor/socket.io')

class Spotio
  constructor: (@port) ->
    @status = {track: "No track playing"}
    @server = http.createServer (req, res) ->
      
  
  
  start: ->
    s = this
    @server.listen(8081)
    @socket = io.listen @server
    
    @socket.on 'connection', (client) ->
      client.send s.get_status()
      
      client.on 'message', (message) ->
        switch message
          when "previous-track"
            s.status["track"] = "previous track"
          when "play-pause-track"
            s.status["track"] = "play/pause track"
          when "next-track"
            s.status["track"] = "next track"
        
        s.broadcast s.get_status()
      
  
  
  broadcast: (message) ->
    @socket.broadcast message
    
  
  
  get_status: ->
    JSON.stringify @status


module.exports = Spotio
    