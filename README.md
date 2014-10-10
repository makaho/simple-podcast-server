Simple Podcast Server
=====================

[![Flattr this git repo](http://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=makaho&url=https://github.com/makaho/simple-podcast-server&title=Simple Podcast Server&language=en&tags=github&category=software) 

Motivation
----------


Features
--------


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
