//
//  SpotioServer.h
//  Spotio
//

#import <Cocoa/Cocoa.h>
#import "AsyncSocket.h"


@interface SpotioServer : NSObject {
	AsyncSocket *listenSocket;
  NSMutableArray *connectedSockets;
  
  BOOL isRunning;
}

- (void)start;
- (void)stop;
- (void)sendStatusToSock:(AsyncSocket *)sock;

@end
