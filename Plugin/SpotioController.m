//
//  SpotioController.m
//  Spotio
//

#import <objc/objc-class.h>
#import "SpotioController.h"
#import "SpotioLogger.h"


#define FORMAT(format, ...) [NSString stringWithFormat:(format), ##__VA_ARGS__]


@implementation SpotioController

@synthesize currentTrack, server;


- (SpotioController*)initWithServer
{
  self = [super init];
  
  server = [[SpotioServer alloc] init];
  [server start];
  
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
  [super dealloc];
}


- (void)startNewTrack:(NSString *)trackName
{
  [SpotioLogger log:FORMAT(@"Track started: \"%@\"", trackName)];
  currentTrack = trackName;
}


- (void)previousTrack
{
  
}


- (void)nextTrack
{
  
}


- (void)playPauseTrack
{
  
}

@end
