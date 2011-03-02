#Spotio

Spotio is an open-source, web-based Spotify remote. The aim is to provide a scalable solution to multi-user/workstation control of a single Spotify installation.

_This version of Spotio is an extremely early developer preview. Features are mostly incomplete and installation is a real pain. **Tl;dr** -- Here be dragons!_


##System Requirements

* Mac OS X 10.6. 10.5 might work, but it won't be officially supported.
* Spotify
* [Simbl](http://www.culater.net/software/SIMBL/SIMBL.php)
* [Rake](http://rake.rubyforge.org/) for installation from source.
* [Node](http://nodejs.org/)
* [Npm](http://npmjs.org/) for CoffeeScript, Express & Eco modules.


##Recommended Browsers

To get the best out of Spotio it is recommeded that you use a modern web browser. Spotio is tested on Chrome 11, Safari 5, Firefox 4 and Mobile Safari (iOS 4.2.1+). Internet Explorer isn't, and never will be, supported.


##How Does Spotio Work?

Spotio is comprised of two parts -- a SIMBL bundle that creates an API and a frontend Node server that then allows multiple clients to work with the API.

When Spotify is run SIMBL injects the Spotio bundle into the process. Our bundle then creates a socket (bound to 127.0.0.1:8079) that listens for commands from clients. Current API methods are:

* `previous-track`
* `next-track`
* `play-pause-track`

Even though these methods are published here please don't rely on them as they may change as we move to a release candidate.

The Spotio bundle is completely client agnostic. Feel free to try out the API with netcat (other network tools are available) -- `nc -vv 127.0.0.1 8079`.


##Installation From Source

Before attempting to install Spotio make sure you've installed all the required software and either downloaded or cloned the latest source:

    $ git clone git://github.com/mattkirman/spotio.git
    $ cd spotio

You can then build and install the Spotify SIMBL bundle with:

    $ rake plugin:build
    $ rake plugin:install

Restart your Spotify client for the changes to take effect. Once Spotify is up and running you can then fire up the Spotify Node server:

    $ cd Node && coffee server.coffee

In your browser navigate to [127.0.0.1:8080](http://127.0.0.1:8080) to start using Spotio.
