# Spotio

Spotio is an open-source, web-based Spotify remote. The aim is to provide a scalable solution to multi-user/workstation control of a single Spotify installation.

_This version of Spotio is an extremely early alpha. Features are mostly incomplete and installation is a real pain. **Tl;dr** -- Here be dragons!_


## System Requirements

* Mac OS X 10.6 -- 10.5 might work, but it will never be officially supported.
* Spotify
* [SIMBL](http://www.culater.net/software/SIMBL/SIMBL.php)
* [Node](http://nodejs.org/)
* [Npm](http://npmjs.org/) for CoffeeScript, Express & Eco modules.
* [Rake](http://rake.rubyforge.org/) for installation from source.


## Recommended Browsers

To get the best out of Spotio it is recommeded that you use a modern web browser. Spotio has been verified to work on Chrome 11. Safari 5, Firefox 4 and Mobile Safari (iOS 4.2.1+) may work, but they are still awaiting testing. Internet Explorer isn't, and never will be, supported.


## How Does Spotio Work?

Spotio is comprised of two parts -- a SIMBL bundle that creates an API and a frontend Node server that then acts as a proxy for multiple clients to work with the API.

When Spotify is run SIMBL injects the Spotio bundle into the process. Our bundle then creates a socket (bound to 127.0.0.1:8079) that listens for commands from our Node proxy server. Current API methods are:

* `previous-track`
* `next-track`
* `play-pause-track`

Even though these methods are published here please don't rely on them as they may change as we move towards a release candidate.

The Spotio bundle is completely client agnostic. Feel free to try out the API with netcat (other network tools are available) -- `nc -vv 127.0.0.1 8079`.


## Installation From Source

Before attempting to install Spotio make sure you've installed all the required software and either downloaded or cloned the latest source:

    $ git clone git://github.com/mattkirman/spotio.git
    $ cd spotio

You can then build and install the Spotify SIMBL bundle with:

    $ rake plugin:build
    $ rake plugin:install


## Pre-compiled Binaries

If you don't feel like compiling the SIMBL plugin from source you can download the latest version directly from [GitHub](https://github.com/mattkirman/spotio/downloads). You will then need to copy it manually into your plugin directory (usually `~/Library/Application Support/SIMBL/Plugins`).


## Usage

Once you have installed Spotio you will have to restart your Spotify client for the changes to take effect. Once Spotify is up and running you can then fire up the Spotify Node server:

    $ cd Node && coffee server.coffee

In your browser navigate to [127.0.0.1:8080](http://127.0.0.1:8080) to start using Spotio.


## Known Issues & Enhancements

* Cross-browser support. Currently only tested in latest Chrome dev builds.
* Create an easy to use installer as it's currently a real dog.
* The Node proxy server fails to run if Spotify isn't already running.


## Contributing

This project was created as a way for me to learn/improve my Node and Objective-C skills. Without doubt things can be improved, so if you would like to lend a hand simply fork the repository and send me a pull request.

If you're not on GitHub feel free to email me at <matt@mattkirman.com>.


## License

Spotio is released under the MIT license.
