HOST = null; // localhost
PORT = 8080;

var http = require("http"),
    url = require("url"),
    fs = require("fs"),
    sys = require("sys"),
    io = require("./lib/Socket.IO-node");


var server = http.createServer(function(req, res){});
server.listen(PORT, HOST)


var socket = io.listen(server);
var _client;

socket.on('connection', function(client){
  _client = client;
  onConnection(client);
  
  client.on('message', function(data){
    onMessage(data, client);
  });
  
  client.on('disconnect', function(data){
    onDisconnect(data, client);
  });
  
  setInterval(pingClients, 250);
});


var onConnection = function(client){
  client.send(statusPacket());
};


var onMessage = function(data, client){
  
};


var onDisconnect = function(data, client){
  
};


var statusPacket = function(){
  return JSON.stringify({
    is_playing: false,
    now_playing: "Lorizzle pot dolor sit amet, doggy adipiscing elit.",
  });
};


var pingClients = function(){
  _client.broadcast(statusPacket());
  _client.send(statusPacket());
};
