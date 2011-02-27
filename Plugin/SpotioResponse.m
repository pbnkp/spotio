//
//  SpotioResponse.m
//  Spotio
//

#import "SpotioResponse.h"


@implementation SpotioResponse

@synthesize _outputStream;


- (SpotioResponse *)initWithOutputStream:(NSOutputStream *)outputStream
{
  assert(outputStream != nil);
  
  self = [super init];
  if (self != nil) {
    self->_outputStream = [outputStream retain];
  }
  return self;
}


- (void)dealloc
{
  [self->_outputStream release];
  [super dealloc];
}


- (void)stream:(NSStream *)aStream handleEvent:(NSStreamEvent)eventCode
// An NSStream delegate callback that's called when events happen on our TCP stream.
{
  assert([NSThread isMainThread]);
  assert(aStream == self._outputStream);
  
  switch (eventCode) {
    case NSStreamEventOpenCompleted: {
      // Do nothing.
    } break;
    
    case NSStreamEventHasBytesAvailable: {
      assert(NO);
    } break;
		
    case NSStreamEventHasSpaceAvailable: {
      
    } break;

  }
}

@end
