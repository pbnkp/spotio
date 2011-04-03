//
//  SpotioController.m
//  Spotio
//

#import <objc/objc-class.h>
#import "SpotioController.h"
#import "SpotioLogger.h"
#import "SPGrowlDelegate+Spotio.h"


#define FORMAT(format, ...) [NSString stringWithFormat:(format), ##__VA_ARGS__]


@implementation SpotioController

@synthesize currentTrack, currentArtist, server;


- (SpotioController*)initWithServer
{
  self = [super init];
  
  // Set up some default values for our track and artist
  currentTrack = @"";
  currentArtist = @"";
  
  server = [[SpotioServer alloc] init];
  [server start];
  
  [SPGrowlDelegate initSpotio];
  
  return self;
}


+ (void)load
{
  [SpotioController sharedInstance];
  [SpotioLogger log:@"installed"];
}


+ (SpotioController*)sharedInstance
{
  static SpotioController *plugin = nil;
  
  if (!plugin) {
    plugin = [[SpotioController alloc] initWithServer];
  }
  
  return plugin;
}


+ (BOOL)swapMethod:(SEL)firstSelector withMethod:(SEL)secondSelector onClass:(Class)class
{
  Method firstMethod, secondMethod;
  
  firstMethod = class_getInstanceMethod(class, firstSelector);
  secondMethod = class_getInstanceMethod(class, secondSelector);
  
  if (firstMethod == nil || secondMethod == nil) {
    return NO;
  }
  
  method_exchangeImplementations(firstMethod, secondMethod);
  return YES;
}


- (void)dealloc
{
  [currentTrack release], currentTrack = nil;
  [currentArtist release], currentArtist = nil;
  [super dealloc];
}


- (void)startNewTrack:(NSString *)trackName byArtist:(NSString *)artistName
{
  [SpotioLogger log:FORMAT(@"Track started: %@ by %@", trackName, artistName)];
  
  currentTrack = [trackName copy];
  currentArtist = [artistName copy];
  
  // Attempt to push this change to our Node app
  [server broadcastStatus];
}


- (void)previousTrack
{
  [SpotioLogger log:@"Previous track"];
}


- (void)nextTrack
{
  [SpotioLogger log:@"Next track"];
}


- (void)playPauseTrack
{
  [SpotioLogger log:@"Play/pause track"];
}

@end
