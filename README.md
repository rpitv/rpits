# RPI TV Titling System
An Open Source titling and character generator system for television stations. Provides simple titling solutions for general use and also heavy support for sports productions. If live sports coverage is your thing you may also want to check out our sister project [RPI TV Scoreboard](https://github.com/rpitv/scoreboard).
## Table of contents
  * [RPI TV Titling System](#rpi-tv-titling-system)
  * [Table of contents](#table-of-contents)
  * [Installation](#installation)
    * [Dependencies](#dependencies)
  * [Usage](#usage)
    * [Setting up an Event](#setting-up-an-event)
    * [Using the Live UI](#using-the-live-ui)
  * [License](#license)
    * [FOSS Inclusions](#foss-inclusions)
## Installation
For full installation details please see [INSTALL](https://github.com/rpitv/rpits/blob/master/INSTALL).
### Dependencies
* [Apache 2](https://httpd.apache.org/) ([Apache 2.0](http://www.apache.org/licenses/LICENSE-2.0)) - RPITS is a web-based program, so it needs an Apache/PHP/MySQL stack to run (specifically apache2, libapache2-mod-php5, php5-mysql, php5-imagick).
* [ImageMagick Imagick PHP Extension](http://pecl.php.net/package/imagick) ([PHP 3.01](http://www.php.net/license/3_01.txt)) - ImageMagick is used for rendering of titles, especially its robust text-resizing abilities.
* [Exavideo Exacore Keyer](https://github.com/exavideo/exacore) ([GPL-3.0](https://www.gnu.org/licenses/gpl-3.0.en.html)) - The Exacore keyer is used for overlaying titles onto a video feed, this not needed if RPITS is being used solely as a post-production title generator.
## Usage
RPITS can be used both as a live production control panel and as a post-production tool, although the feature set is heavily weighed towards the former. Full usage documentation will be added here in the future, in the meantime there wll just be scaffolding describing general features and abilites.
### Setting up an Event
RPITS organizes titles based on events, whether it be a hockey game, debate, concert, or something else entirely. Time should be taken prior to an event to add title templates to the event, add information to those titles, and render everything that can be rendered. In the case of sporting events organization, team, and player information should be added and updated as well.
### Using the Live UI
During an event the Live UI is the control panel for previewing and outputting graphics onto your live video feed. Ideally everything that needs to be done on the fly can be done in this interface, whether it be updating title text, adding another template, or rendering a title.
## License
RPITS is licensed under MIT: [LICENSE](https://github.com/rpitv/rpits/blob/master/LICENSE)
### FOSS Inclusions
The following are included, whole or in part, within this project. All included materials must have compatible licenses.
* [jQuery](http://jquery.com/) - [MIT](https://jquery.org/license/)
* [jQuery UI](http://jqueryui.com/) - [MIT](https://jquery.org/license/)
* [jQuery scrollintoview](http://erraticdev.blogspot.com/2011/02/jquery-scroll-into-view-plugin-with.html) - [MIT](https://opensource.org/licenses/MIT)
* [jQuery Color Animation](https://www.bitstorm.org/jquery/color-animation/) - [MIT/GPL](https://www.bitstorm.org/jquery/license.html)
* [Open Iconic](https://useiconic.com/open/) - [Open Font](http://scripts.sil.org/cms/scripts/page.php?item_id=OFL_web) (font) / [MIT](https://opensource.org/licenses/MIT) (icons)
* [JSColor](http://jscolor.com/) - [LGPL](https://www.gnu.org/copyleft/lesser.html)
* [Pit picture](https://www.flickr.com/photos/jaymiek/2624447801) - Jaymie Koroluk [CC BY-NC-SA 2.0](https://creativecommons.org/licenses/by-nc-sa/2.0/)

