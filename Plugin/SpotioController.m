//
//  SpotioController.m
//  Spotio
//

#import <objc/objc-class.h>
#import "SpotioController.h"


@implementation SpotioController

@synthesize currentTrack, httpServer;


- (SpotioController*)initWithServer
{
  self = [super init];
  
  httpServer = [HTTPServer sharedInstance];
  [httpServer start];
  
  return self;
}


+ (void)load
{
  [SpotioController sharedInstance];
  NSLog(@"Spotio installed");
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
  NSLog(@"Spotio: Track started: \"%@\"", trackName);
  currentTrack = trackName;
}

@end
