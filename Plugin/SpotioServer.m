//
//  SpotioServer.m
//  Spotio
//

#import "SpotioServer.h"
#import "SpotioLogger.h"


#define FORMAT(format, ...) [NSString stringWithFormat:(format), ##__VA_ARGS__]


@implementation SpotioServer

- (id)init
{
  if ((self = [super init])) {
    listenSocket = [[AsyncSocket alloc] initWithDelegate:self];
    connectedSockets = [[NSMutableArray alloc] initWithCapacity:1];
    isRunning = NO;
  }
  return self;
}


- (void)start
{
  if (!isRunning) {
    int port = 8079;
    NSError *error = nil;
    
    if (![listenSocket acceptOnPort:port error:&error]) {
      [SpotioLogger log:FORMAT(@"Error starting server: %@", error)];
      return;
    }
    
    [SpotioLogger log:FORMAT(@"Server started on port %hu", [listenSocket localPort])];
    isRunning = YES;
  }
}


- (void)stop
{
  if (isRunning) {
    // Stop accepting connections
    [listenSocket disconnect];
    
    // Stop any client connections
    NSUInteger i;
    for (i = 0; i < [connectedSockets count]; i++) {
      // Call disconnect on the socket
      [[connectedSockets objectAtIndex:i] disconnect];
    }
    
    [SpotioLogger log:@"Stopped the server"];
    isRunning = NO;
  }
}


- (void)onSocket:(AsyncSocket *)sock didAcceptNewSocket:(AsyncSocket *)newSocket
{
  [connectedSockets addObject:newSocket];
}


- (void)onSocket:(AsyncSocket *)sock didConnectToHost:(NSString *)host port:(UInt16)port
{
  [SpotioLogger log:FORMAT(@"Accepted client %@:%hu", host, port)];
  
  NSString *status = @"{\"track\":\"Spotify Hello World\"}";
  NSData *data = [status dataUsingEncoding:NSUTF8StringEncoding];
  
  [sock writeData:data withTimeout:-1 tag:0];
  [sock readDataToData:[AsyncSocket LFData] withTimeout:-1 tag:0];
}


- (void)onSocket:(AsyncSocket *)sock didReadData:(NSData *)data withTag:(long)tag
{
  NSData *strData = [data subdataWithRange:NSMakeRange(0, [data length] - 1)];
  NSString *msg = [[[NSString alloc] initWithData:strData encoding:NSUTF8StringEncoding] autorelease];
  
  if (msg) {
    [SpotioLogger log:msg];
    
    NSString *response = FORMAT(@"{\"track\":\"%@\"}", msg);
    NSData *data = [response dataUsingEncoding:NSUTF8StringEncoding];
    
    [sock writeData:data withTimeout:-1 tag:0];
  } else {
    [SpotioLogger log:@"Error converting received data into UTF-8 string"];
  }
	
  [sock readDataToData:[AsyncSocket LFData] withTimeout:-1 tag:0];
}


- (void)onSocket:(AsyncSocket *)sock willDisconnectWithError:(NSError *)error
{
  [SpotioLogger log:FORMAT(@"Client disconnected : %@:%hu", [sock connectedHost], [sock connectedPort])];
}


- (void)onSocketDidDisconnect:(AsyncSocket *)sock
{
  [connectedSockets removeObject:sock];
}

@end
