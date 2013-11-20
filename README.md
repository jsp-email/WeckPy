# Raspberry Pi Alarm

Webinterface in Bootstrap-Style with small modifications in the Flat-UI Style for the Raspberry Pi

## Features

* Alarm System (python daemon need to be run) 
* Control remote power supplies (adapted by powerpi [http://raspberrypiguide.de/howtos/powerpi-raspberry-pi-haussteuerung/](http://raspberrypiguide.de/howtos/powerpi-raspberry-pi-haussteuerung/))
* Display Pi Status (adapted by Raspberry Pi Control Center [http://rpi-controlcenter.de/](http://rpi-controlcenter.de/)) 
* MPD Music Control (adapted by sn0opy MPD-Webinterface [https://github.com/sn0opy/MPD-Webinterface](https://github.com/sn0opy/MPD-Webinterface), [http://mpd.24oz.com/](http://mpd.24oz.com/))
* Link to owncloud Installation


## Installation

### Alarm
* copy all files in www to your htdocs
* copy all files in daemon to a directory on your server (etc. /home/pi)
* set the path to the Configuration xml File in `/www/lib/weckerpy.php` in the function getWecker() 
* start the daemon by calling `python /home/pi/weckerApp.py start`

### Other
* Install Powerpi and WiringPi [http://raspberrypiguide.de/howtos/powerpi-raspberry-pi-haussteuerung/](http://raspberrypiguide.de/howtos/powerpi-raspberry-pi-haussteuerung/)


## Thx to
* powerpi [http://raspberrypiguide.de/howtos/powerpi-raspberry-pi-haussteuerung/](http://raspberrypiguide.de/howtos/powerpi-raspberry-pi-haussteuerung/)
* Raspberry Pi Control Center [http://rpi-controlcenter.de/](http://rpi-controlcenter.de/)                  
* sn0opy MPD-Webinterface [https://github.com/sn0opy/MPD-Webinterface](https://github.com/sn0opy/MPD-Webinterface), [http://mpd.24oz.com/](http://mpd.24oz.com/)
