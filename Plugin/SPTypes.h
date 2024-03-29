//
//  SPTypes.h
//  Spotio
//

struct ArtistInfo;

struct FileId {
  unsigned char _field1[20];
};

struct MusicFormat {
  unsigned char _field1;
  unsigned char _field2;
  unsigned char _field3;
  unsigned char _field4;
  int _field5;
};

struct RefPtr_AlbumInfo {
  struct AlbumInfo *_field1;
};

struct RefPtr_ArtistInfo {
  struct ArtistInfo *_field1;
};

struct RefPtr_TrackInfo {
  struct TrackInfo *_field1;
};

struct TrackId {
  unsigned char _field1[16];
};

struct VersionAndExpiry {
  unsigned int _field1;
  unsigned int _field2;
};

struct TrackInfo {
  void **_field1;
  long _field2;
  void *_field3;
  unsigned int _field4;
  struct TrackId _field5;
  struct FileId _field6;
  struct MusicFormat _field7;
  struct RefPtr_ArtistInfo _field8;
  struct ArtistInfo **_field9;
  struct RefPtr_AlbumInfo _field10;
  struct RefPtr_TrackInfo _field11;
  unsigned char _field12;
  unsigned char _field13;
  unsigned int a;
  unsigned int b;
  unsigned int c;
  unsigned int d;
  unsigned int e;
  unsigned int f;
  unsigned int g;
  unsigned char _field14;
  unsigned char _field15;
  struct VersionAndExpiry _field16;
  struct PurchaseLinks *_field17;
};
