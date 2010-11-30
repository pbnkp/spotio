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
socket.on('connection', function(client){
  onConnection(client);
  
  client.on('message', function(data){
    onMessage(data, client);
  });
  
  client.on('disconnect', function(data){
    onDisconnect(data, client);
  });
});


var onConnection = function(client){
  
};


var onMessage = function(data, client){
  
};


var onDisconnect = function(data, client){
  
};
