import os
import socket
import time
import xml.dom.minidom
import datetime
from threading import Thread


from urllib2 import Request, urlopen
import json
import subprocess
import logging

class wecker:
    def __init__(self):
        self.IP_Smartphone = '192.168.1.100'
        self.alarmsfile = "/var/www/wecker/alarms.xml"
        self.startVol = 50
        self.stopVol = 70
        self.incVol = 2
        # Duration of Increment the Volume in seconds
        self.incVolDuration = 20
        self.weckerAn = False
        self.active_Wecker = dict()
        #self.logger = logger

    def istSmartphoneImWlan(self, rezerenzIp):
        i = 0
        n = 2
        while i <= n:
            if(os.system('ping -c 3 ' + rezerenzIp) == 0):
                i = i + 1
                return True
            else:
               i = i + 1
        return False

    def schalte(self, receiver, status):
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(('127.0.0.1', 6677))
        s.send('setsocket:'+receiver+':'+str(status))
        s.close()

    def weckeAuf(self, song = 1):
        logger = logging.getLogger("DaemonLog")
        logger.info("Wecke auf "+str(self.active_Wecker))
        os.system("mpd")
        os.system("mpc clear")
        os.system("mpc load "+self.active_Wecker['Playlist'])
        os.system("mpc volume "+str(self.active_Wecker['startVol']))
        
        for supply in self.active_Wecker['Power-On']:
          self.schalte(supply, 1)
          
        if 'TTS' in self.active_Wecker:
          # Additional Infos
          req = Request("http://api.openweathermap.org/data/2.5/weather?q=Ruit&units=metric&lang=de")
          response = urlopen(req)
          the_page = json.load(response)
          wetter = the_page['weather'][0]['description'].encode('utf-8')
          temp = str(int(the_page['main']['temp']))
          uhrzeit = str(datetime.datetime.strftime(datetime.datetime.now(), '%H Uhr %M'))+" "
          tts = self.active_Wecker['TTS'].format(wetter=wetter, temp = temp, time=uhrzeit)         
          destination_language = 'de'
          googleSpeechURL = "http://translate.google.com/translate_tts?tl=" + destination_language +"&q=" + tts + "&ie=UTF-8"
          print googleSpeechURL
          logger.info(googleSpeechURL)
          subprocess.call(["mplayer",googleSpeechURL,"-af","volume=8"], shell=False, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
          #os.system("mplayer "+googleSpeechURL+" -af volume=8")
          #time.sleep(5)
          
        os.system("mpc play "+str(song))
        
        if self.active_Wecker['Fade'] == 1:
          logger.info("Fade begins")
          t = Thread(target=self.incVolume, args=(self.active_Wecker['startVol'],self.active_Wecker['endVol'], self.incVolDuration,))
          t.start()
        

    def incVolume(self, startvol, endvol, sleepduration):
        logger = logging.getLogger("DaemonLog")
        logger.info("Start Fading...")
        print "Start Fading.."
        vol = startvol
        while(vol <= endvol):
            if self.weckerAn == False:
                exit()
            time.sleep(sleepduration)
            logger.info("Fade "+str(vol)+" -> "+str(vol+ self.incVol))
            print "Fade "+str(vol)+" -> "+str(vol+ self.incVol)            
            vol = vol + self.incVol
            os.system("mpc volume "+str(vol))
            

    def beendeWecker(self):
        os.system("mpc stop ")
        for supply in self.active_Wecker['Power-Off']:
          self.schalte(supply, 0)
          
        self.weckerAn = False
        self.active_Wecker = dict()
        
    def leseAlarms(self):
        alarms = dict()
        if os.path.exists(self.alarmsfile):
            datei = open(self.alarmsfile, "r")
            dom = xml.dom.minidom.parse(datei)
            datei.close()
            root = dom.getElementsByTagName('Wecker')
            
            for ind, wecker in enumerate(root):
                alarms[ind] = dict()
                
                wochentage = wecker.getElementsByTagName( "Weekdays" )
                wochentage_arr = []
                for tag in wochentage:
                    if tag.hasChildNodes():
                          for wochentag in tag.childNodes:
                              wochentage_arr.append(wochentag.getAttribute("name"))
                alarms[ind]['Wochentage'] = wochentage_arr
                
                alarms[ind]['uhrzeit'] = wecker.getElementsByTagName( "Uhrzeit" )[0].firstChild.nodeValue
                alarms[ind]['Playlist'] = wecker.getElementsByTagName( "Playlist" )[0].firstChild.nodeValue
                alarms[ind]['Active'] = int(wecker.getElementsByTagName( "Active" )[0].firstChild.nodeValue)
                alarms[ind]['Fade'] = int(wecker.getElementsByTagName( "Fade" )[0].firstChild.nodeValue)
                alarms[ind]['Dauer'] = int(wecker.getElementsByTagName( "Dauer" )[0].firstChild.nodeValue)
                alarms[ind]['id'] = int(wecker.getAttribute("id"))
                alarms[ind]['startVol'] = int(wecker.getElementsByTagName( "startVol" )[0].firstChild.nodeValue)
                alarms[ind]['endVol'] = int(wecker.getElementsByTagName( "endVol" )[0].firstChild.nodeValue)
                if  wecker.getElementsByTagName( "TTS" ):
                  alarms[ind]['TTS'] = wecker.getElementsByTagName( "TTS" )[0].firstChild.nodeValue
                
                power_supplies_on = wecker.getElementsByTagName( "Power-On" )
                supply_on_arr = []
                for tag in power_supplies_on:
                    if tag.hasChildNodes():
                          for supply in tag.childNodes:
                              supply_on_arr.append(supply.getAttribute("name"))
                alarms[ind]['Power-On'] = supply_on_arr
                
                power_supplies_off = wecker.getElementsByTagName( "Power-Off" )
                supply_off_arr = []
                for tag in power_supplies_off:
                    if tag.hasChildNodes():
                          for supply in tag.childNodes:
                              supply_off_arr.append(supply.getAttribute("name"))
                alarms[ind]['Power-Off'] = supply_off_arr
        
        return alarms
        
    def checkAlarm(self):
        logger = logging.getLogger("DaemonLog")
        logger.debug("Checkalarm in class "+str(self.weckerAn))
        if self.weckerAn == False:
            alarms = self.leseAlarms()
            wochentag_jetzt = datetime.datetime.strftime(datetime.datetime.now(), '%A')
            for wecker in alarms.itervalues():
                if wecker['Active'] == 1:
                    for tag in wecker['Wochentage']:
                        #logger.debug("Wochentag Jetzt "+  wochentag_jetzt)
                        #logger.debug("DEBUG Wochentag Test "+  tag)
                        if tag == wochentag_jetzt:
                            #print "DEBUG Wochentag stimmt"
                            uhrzeit_jetzt = datetime.datetime.strftime(datetime.datetime.now(), '%H:%M')
                            uhrzeit_wecker = wecker['uhrzeit']
                            minutes = lambda zeit:sum([int(v)*60**(1-n) for n,v in enumerate(zeit.split(":"))])
                            #print "DEBUG Uhrzeit Jetzt "+  uhrzeit_jetzt
                            #print "DEBUG Uhrzeit Wecker "+  uhrzeit_wecker
                            if minutes(uhrzeit_wecker) == minutes(uhrzeit_jetzt):
                                if(self.istSmartphoneImWlan(self.IP_Smartphone)):
                                    print "Wecker ist jetzt an"
                                    logger.info("Wecker "+str(wecker)+" ist jetzt an")
                                    self.active_Wecker = wecker
                                    self.weckeAuf()
                                    if wecker['Dauer'] == 0:
                                        return -1
                                    else:
                                        self.weckerAn = True
                                        return self.active_Wecker['Dauer']
        return False
        
if __name__ == '__main__':
    
    w = wecker()


   
