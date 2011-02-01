Take your MP3 or Ogg based music library anywhere with a modern web browser!
Streeme offers a secure, private and legal way to serve your personal music
collection online without purchasing expensive apps or hardware. Using the
powerful Symfony php framework and MySQL 5, this app is a snap to install and
run for yourself. Please download it, hack on it and make it better for
everyone!

LICENSE
-------
See the license file LICENSE.TXT

INSTALLATION
------------
See the installation file INSTALL.TXT 

CARE AND FEEDING OF YOUR STREEME
--------------------------------
See the manual file in the Wiki - it's bound to change a lot

In case the wiki is unavailable, here's a short guide 

Loading your media library 
---
this can be done from the commandline in the project dir
You will need to fill out your app.yml with watched folders or an itunes path
(note: on windows, the symfony command is php symfony instead of ./symfony)

cd path/to/streeme

./symfony scan-media --type=itunes
OR
./symfony scan-media --type=filesystem

Getting art for your music can be done in the following ways 

from ID3 tags
./symfony scan-art --source=meta 

from Amazon's PAS system for amazon associates
./symfony scan-art --source=amazon

from folders that the mp3s live in 
./symfony scan-art --source=folders

keep in mind that support for other music and list formats is an ongoing battle
this doc may be horribly out of date already :) You should run these scripts
every so often so Streeme stays up to date.

Creating a user
--- 
you should create a user for each device you'll use with streeme
to do that it's back to the commandline 

./symfony guard:create-user username password 

Logging in
---
When you log in, you'll be presented with a few options
  -Mobile player 
  -Desktop player
  -Logout

The mobile player is optimized for a touch screen interface and the desktop
player is optimized for features and bigger screen sizes.

both players offer options for transcoding / modifying bitrate in their
settings - please install FFMPEG if you would like to use these features for
low bandwidth connections or changing formats. 

How to use the player 
---
If you've done everything right, you should see your content properly scanned
and ready to use. the mobile player is navigated by tapping on albums, artists,
songs, playlists, genres etc. HTML5 is very much in its infancy, so not all
mobile players have the prescribed controls. The buttons beside the album art
skip tracks and tapping the album art will pause and play. when the || symbol
appears, the playback is paused. Press it again to resume playback. Playlists
created in the desktop app can be used in the mobile app.

The desktop player has different actions for single and double clicks. a
double click on the song table will begin playing alternatively, you can press
the single play button to the left of the song name. The RND button will
shuffle the songs and the forward and previous buttons will skip tracks. 

Searches in either interface will return search results including all text 
columns of the table.  

Building playlists
---
The desktop player has the ability to form playlists. the little + symbols next
to your music items will add the songs, albums and artists to your selected
playlist. To select a playlist click the folder icon in the top right, select 
add playlist and click it once to select it. It will turn a faint blue when
active. You will now be able to add songs to the playlist. You can easily
change the active playlist by single clikcing on any playlist in the list.
To play back the playlist, you can click the play symbol or double click the 
playlist list entry.

Tested Browsers ( December 2010 )
---
Chrome Browser 7.0.517.44 ( Full )

Mozilla Firefox 3.6 ( Partial - no mp3 codec must use ogg transcoding )

Safari 5.x series ( Full )

IE9 Beta ( Partial - no integrated controls )

iOS Mobile Safari ( 4.2 Full )
 
WebOS ( Palm Pre 1.4 Partial)
No integrated controls - only specific MP3 formats allowed - audio tag does
not send cookies, so it may be a security risk - no ogg support
  
Android ( API V8 - ver 2.2.1 None ) 

Credits
---
Thanks to Ryan Cumming for designing the model and scanner logic for
streampod (previous project name) and streeme!    

Copyright Notice
---
The artwork included with this package is copyright Richard Hoar (The Author)
it may be reproduced for non-commercial purposes without asking permission from
the Author or paying copyright royalty.