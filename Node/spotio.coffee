http = require 'http'
net = require 'net'
spawn = require('child_process').spawn
io = require './vendor/socket.io'

class Spotio
  constructor: (@port) ->
    s = this
    @status = {track: "Connecting...", artist: ""}
    
    @control = net.createConnection 8079
    @control.setEncoding 'utf8'
    
    @control.on 'data', (data) ->
      s.parseMessage data
    
    @server = http.createServer (req, res) ->
  
  
  start: ->
    s = this
    @server.listen(8081)
    @websocket = io.listen @server
    
    @websocket.on 'connection', (client) ->
      client.send s.getStatus()
      
      client.on 'message', (message) ->
        s.queryControl message
  
  
  broadcast: (message) ->
    @websocket.broadcast message
  
  
  getStatus: ->
    JSON.stringify @status
  
  
  queryControl: (command) ->
    switch command
      when 'play-pause-track' then spawn 'osascript', ['bin/play_pause.applescript']
      when 'next-track' then spawn 'osascript', ['bin/next_track.applescript']
      when 'previous-track' then spawn 'osascript', ['bin/previous_track.applescript']
      
    #@control.write "#{command}\n"
  
  
  parseMessage: (message) ->
    try
      message = JSON.parse message.split("\n")[0]
      @status["track"] = message.track
      @status["artist"] = message.artist
    catch error
      console.log "Error parsing JSON response"
    finally
      this.broadcast this.getStatus()


module.exports = Spotio
    