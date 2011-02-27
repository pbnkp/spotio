//
//  HTTPServer.h
//  Spotio
//

#import <Foundation/Foundation.h>


@interface HTTPServer : NSObject {
	BOOL running;
  CFSocketRef listeningSocket;
}

@property(nonatomic, assign, readonly, getter=isRunning) BOOL running;

@property (nonatomic, retain, readwrite) NSNetService* netService;

+(HTTPServer*)sharedInstance;

-(void)start;
-(void)stop;
-(void)stopWithStatus:(NSString *)status;

@end
