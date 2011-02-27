//
//  HTTPServer.m
//  Spotio
//

#import "HTTPServer.h"
#import "SpotioResponse.h"

#include <sys/socket.h>
#include <netinet/in.h>


@implementation HTTPServer

@synthesize running;


+ (void)load
{
  [HTTPServer sharedInstance];
  NSLog(@"Spotio HTTP server initialised");
}


+ (HTTPServer*)sharedInstance
{
  static HTTPServer *server = nil;
  
  if (!server)
    server = [[HTTPServer alloc] init];
  
  return server;
}


- (void)start
{
  int err;
  int fdForListening;
  int chosenPort;
  socklen_t namelen;
  
  assert(!self.isRunning);
  assert(listeningSocket == NULL);
  
  // Currently we only support IPv4. This shouldn't be an issue shortterm as we're
  // only going to be listening for connections from localhost.
  struct sockaddr_in serverAddress;
  err = 0;
  
  fdForListening = socket(AF_INET, SOCK_STREAM, 0);
  if (fdForListening < 0) {
    err = errno;
  }
  
  if (err == 0) {
    memset(&serverAddress, 0, sizeof(serverAddress));
    serverAddress.sin_family = AF_INET;
    serverAddress.sin_len = sizeof(serverAddress);
    
    err = bind(fdForListening, (const struct sockaddr *) &serverAddress, sizeof(serverAddress));
    if (err < 0) {
      err = errno;
    }
  }
  
  if (err == 0) {
    namelen = sizeof(serverAddress);
    err = getsockname(fdForListening, (struct sockaddr *) &serverAddress, &namelen);
    if (err < 0) {
      err = errno;
      assert (err != 0); // Quietens static analyser
    } else {
      chosenPort = ntohs(serverAddress.sin_port);
    }
  }
  
  
  // Listen for connections on our socket, then create a CFSocket to route any
  // connections to a run loop callback.
  if (err == 0) {
    err = listen(fdForListening, 5);
    if (err < 0) {
      err = errno;
    } else {
      CFSocketContext context = {0, self, NULL, NULL, NULL};
      CFRunLoopSourceRef rls;
      
      self->listeningSocket = CFSocketCreateWithNative(NULL, fdForListening, kCFSocketAcceptCallBack, ListeningSocketCallback, &context);
      
      if (self->listeningSocket != NULL) {
        assert(CFSocketGetSocketFlags(self->listeningSocket) & kCFSocketCloseOnInvalidate);
        fdForListening = -1; // so that clean up code doesn't close it
        
        rls = CFSocketCreateRunLoopSource(NULL, self->listeningSocket, 0);
        assert(rls != NULL);
        
        CFRunLoopAddSource(CFRunLoopGetCurrent(), rls, kCFRunLoopDefaultMode);
        CFRelease(rls);
      }
    }
  }
  
  
  // Clean up
  if (self->listeningSocket != NULL && self.netService != nil) {
    running = YES;
  } else {
    [self stopWithStatus:@"Failed to start up."];
  }
  
  if (fdForListening >= 0) {
    close(fdForListening);
  }
}


- (void)stop
{
  if (self.netService != nil) {
    [self.netService setDelegate:nil];
    [self.netService stop];
    self.netService= nil;
  }
  
  if (self->listeningSocket != NULL) {
    CFSocketInvalidate(self->listeningSocket);
    CFRelease(self->listeningSocket);
    self->listeningSocket = NULL;
  }
  
  running = NO;
}


- (void)stopWithStatus:(NSString *)status
{
  NSLog(@"Spotio: HTTPServer: %@", status);
  [self stop];
}


static void ListeningSocketCallback(CFSocketRef s, CFSocketCallBackType type, CFDataRef address, const void *data, void *info)
{
  
}


- (void)connectionReceived:(int)fd
// Called when a connection is received. We respond by creating and running a
// ResponseSendOperation that sends the current status down the connection.
{
  CFWriteStreamRef writeStream;
  SpotioResponse *op;
  Boolean success;
  
  assert(fd >= 0);
  
  // Create a CFStream from the connection socket.
  CFStreamCreatePairWithSocket(NULL, fd, NULL, &writeStream);
  assert(writeStream != nil);
  
  success = CFWriteStreamSetProperty(writeStream, kCFStreamPropertyShouldCloseNativeSocket, kCFBooleanTrue);
  assert(success);
  
  // Create a new SpotioResponse to run the connection.
  op = [[SpotioResponse alloc] initWithOutputStream:(NSOutputStream *)writeStream];
}

@end
