//
//  SpotioController.h
//  Spotio
//

#import <Cocoa/Cocoa.h>
#import "SpotioServer.h"


@interface SpotioController : NSObject {
	NSString *currentTrack;
  NSString *currentArtist;
  SpotioServer *server;
}

@property(readonly, retain) NSString *currentTrack;
@property(readonly, retain) NSString *currentArtist;
@property(readonly, retain) SpotioServer *server;

- (SpotioController*)initWithServer;
+ (SpotioController*)sharedInstance;

- (void)startNewTrack:(NSString*)trackName byArtist:(NSString *)artistName;

+ (BOOL)swapMethod:(SEL)firstSelector withMethod:(SEL)secondSelector onClass:(Class)class;

- (void)nextTrack;
- (void)previousTrack;
- (void)playPauseTrack;

@end
