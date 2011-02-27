//
//  SpotioResponse.h
//  Spotio
//

#import <Cocoa/Cocoa.h>


@interface SpotioResponse : NSObject {
	NSOutputStream *_outputStream;
}

@property(readonly, retain) NSOutputStream *_outputStream;

- (SpotioResponse *)initWithOutputStream:(NSOutputStream *)outputStream;

@end
