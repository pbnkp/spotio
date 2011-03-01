//
//  SpotioLogger.m
//  Spotio
//

#import "SpotioLogger.h"


@implementation SpotioLogger

+ (void)log:(NSString *)message
{
  NSLog(@"Spotio: %@", message);
}

@end
