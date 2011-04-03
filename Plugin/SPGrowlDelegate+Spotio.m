//
//  SPGrowlDelegate+Spotio.m
//  Spotio
//

#import "SPGrowlDelegate+Spotio.h"
#import "SPController.h"
#import "SpotioController.h"
#import "SPTypes.h"
#import "SpotioLogger.h"


@interface SPGrowlDelegate (DummyReplacedMethods)
- (void)_spotio_notificationWithTrackInfo:(void*)info;
@end


@implementation SPGrowlDelegate (Spotio)

+ (void)initSpotio
{
  [SpotioController swapMethod:@selector(notificationWithTrackInfo:) withMethod:@selector(_spotio_notificationWithTrackInfo:) onClass:[self class]];
  [SpotioLogger log:@"Swapped 'notificationWithTrackInfo' method with '_spotio_notificationWithTrackInfo'"];
  
}

- (void)_spotio_notificationWithTrackInfo:(struct TrackInfo*)info
{
  [self _spotio_notificationWithTrackInfo:info];
  
  if (info != NULL) {
    NSString *songTitle = [[NSString alloc] initWithCString:info->_field3	encoding:NSUTF8StringEncoding];
    NSString *dockTitle = [[[[[SPController sharedController] applicationDockMenu:nil] itemArray] objectAtIndex:0] title];
    
    int removeLength = [songTitle length] + 3;
    NSString *artist = [dockTitle stringByReplacingCharactersInRange:NSMakeRange([dockTitle length]-removeLength, removeLength) withString:@""];
    
    [[SpotioController sharedInstance] startNewTrack:songTitle byArtist:artist];
    
    [songTitle release], songTitle = nil;
  }
}

@end
