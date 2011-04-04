sys   = require 'sys'
http  = require 'http'
url   = require 'url'
path  = require 'path'
fs    = require 'fs'

Spotio = require './spotio'

#-----------------------------------------------------------------------------
# UTILITY METHODS

log = (request) ->
  date = new Date
  
  console.log [
    "[" + date.toString() + "]",
    request.connection.remoteAddress,
    request.method,
    request.headers.host + request.url,
    "'" + request.headers['user-agent'] + "'"
  ].join(" ")


#-----------------------------------------------------------------------------
# START THE STATIC FILE SERVER

port = 8080
console.log "Starting server on port " + port

http.createServer (request, response) ->
  uri  = url.parse(request.url).pathname
  uri = uri + "index.html" if uri == "/"
  
  filename = path.join(process.cwd(), "public", uri)
  log request
  
  path.exists filename, (exists) -> 
    unless exists
      response.writeHead 404, {"Content-Type": "text/plain"}
      response.end "404 Not Found\n"
      return
      
    fs.readFile filename, "binary", (err, file) ->
      if err
        response.writeHead 500, {"Content-Type": "text/plain"}
        response.end err + "\n"
        return
        
      response.writeHead 200
      response.end file, "binary"
      
.listen port, "0.0.0.0"

console.log "Server running on port " + port


#-----------------------------------------------------------------------------
# START THE SPOTIO SOCKET SERVER

s = new Spotio
s.start()
