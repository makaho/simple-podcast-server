Simple Podcast Server
=====================

[![Flattr this git repo](http://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=makaho&url=https://github.com/makaho/simple-podcast-server&title=Simple Podcast Server&language=en&tags=github&category=software) 

Motivation
----------

Listening to podcasts is easy across many devices. But if you are only interested in single episodes, things become complicated. Do you subscribe to a podcast download a single episode and cancel the subscription after listening? What if your favourite blog attaches an audio file of an interview? Do you check if you can subscribe to their feed? With the Simple Podcast Server you can create your own podcast feed with episodes selected by you.

You can simply download the audio files of your interest and add them to your feed. The Simple Podcast Server will create a podcast feed to which you can subscribe from any client. If available, the Simple Podcast Server will extract metadata from the file to display "*Captain Crunch*" instead of "*wdr_hoerspiel_download_captain_crunch_20140715_0001.mp3*".

Features
--------

- Creates a standard complaint podcast feed
- Supports HTML5 drang and drop for uploading
- Extracts metadata from files
- Extracts episode cover
- Management interface for organising your media files
- Protection through *.htaccess* files

Installation
------------
- Copy files to server.
- Change .htaccess in root directory to point to your password file / create one.
- Change config.php and enter your values, the especially the URL has to be updated to your value, otherwise the links in the feeds and the admin interface won't work.
- Check your php settings and increase the maximum file size for uploads when needed. You might also need to update the maximum execution for scripts, if the analysis of the files takes too long.

ToDos
-----

- Add help and introduction section to website
- Add mobile layout
- Check filetype before parsing, discard unsupported
- Future: Add server download-feature for URLs
