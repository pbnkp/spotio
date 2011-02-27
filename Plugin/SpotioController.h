//
//  SpotioController.h
//  Spotio
//

#import <Cocoa/Cocoa.h>
#import "HTTPServer.h"


@interface SpotioController : NSObject {
	NSString *currentTrack;
  HTTPServer *httpServer;
}

@property(readonly, retain) NSString *currentTrack;
@property(readonly, retain) HTTPServer *httpServer;

-(SpotioController*)initWithServer;
+(SpotioController*)sharedInstance;

-(void)startNewTrack:(NSString*)trackName;

+(BOOL)swapMethod:(SEL)firstSelector withMethod:(SEL)secondSelector onClass:(Class)class;

@end
