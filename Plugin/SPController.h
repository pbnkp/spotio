/*
 *     Generated by class-dump 3.3.3 (64 bit).
 *
 *     class-dump is Copyright (C) 1997-1998, 2000-2001, 2004-2010 by Steve Nygard.
 */

@interface SPController : NSObject
{
    //SPUndoManager *_undo_manager;
    struct __CFMachPort *_eventPort;
    struct __CFRunLoopSource *_eventPortSource;
    BOOL _shouldInterceptMediaKeyEvents;
}

+ (id)sharedController;
+ (id)allocWithZone:(struct _NSZone *)arg1;
+ (int)windowAtTopOfEvent:(id)arg1;
- (id)copyWithZone:(struct _NSZone *)arg1;
- (id)retain;
- (unsigned int)retainCount;
- (void)release;
- (id)autorelease;
- (void)setFeedbackRegardingButton:(id)arg1;
- (void)setFeedbackScreenshotButton:(id)arg1;
- (void)setFeedbackTitleCell:(id)arg1;
- (void)setFeedbackFreeText:(id)arg1;
- (void)closeFeedbackForm:(id)arg1;
- (void)sendFeedback:(id)arg1;
- (void)resetFeedbackForm;
- (void)processFeedback;
- (void)applicationWillFinishLaunching:(id)arg1;
- (void)applicationDidFinishLaunching:(id)arg1;
- (void)applicationWillTerminate:(id)arg1;
- (BOOL)validateMenuItem:(id)arg1;
- (void)doAction:(id)arg1;
- (void)copy:(id)arg1;
- (void)paste:(id)arg1;
- (void)cut:(id)arg1;
- (void)undo:(id)arg1;
- (void)redo:(id)arg1;
- (void)selectAll:(id)arg1;
- (void)selectNone:(id)arg1;
- (void)delete:(id)arg1;
- (void)getURI:(id)arg1 withReplyEvent:(id)arg2;
- (void)openLink:(id)arg1 userData:(id)arg2 error:(id *)arg3;
- (BOOL)application:(id)arg1 openFile:(id)arg2;
- (void)application:(id)arg1 openFiles:(id)arg2;
- (void)setupWindowAndViews;
- (id)windowWillReturnUndoManager:(id)arg1;
- (BOOL)applicationShouldHandleReopen:(id)arg1 hasVisibleWindows:(BOOL)arg2;
- (void)setSchedulerTimer;
- (void)setWindowController:(struct WindowControllerOSX *)arg1;
- (struct WindowControllerOSX *)windowController;
- (void)startup;
- (id)applicationDockMenu:(id)arg1;
- (void)notificationDelivery:(id)arg1;
- (id)mainWindow;
- (id)feedbackWindow;
- (id)undoManager;
- (void)pasteboard:(id)arg1 provideDataForType:(id)arg2;
- (void)pasteboard:(id)arg1 provideDataForType:(id)arg2 flags:(unsigned int)arg3;
- (void)declareDraggedTypesForPasteboard:(id)arg1;
- (id)dragPasteboard;
- (void)setDragPasteboard:(id)arg1;
- (BOOL)isShuttingDown;
- (void)dealloc;
- (void)prepareForShutdown;
- (void)focusCurrentlyPlayingTrack;

@end

