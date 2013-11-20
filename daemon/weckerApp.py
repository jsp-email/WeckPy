import wecker
import os
import time
from daemon import runner
from datetime import datetime
import sys


class weckerApp():

    def __init__(self):
        self.stdin_path = '/dev/null'
        self.stdout_path = '/dev/tty'
        self.stderr_path = '/dev/tty'
        self.pidfile_path = '/var/run/wecker.pid'
        self.pidfile_timeout = 5
        self.wecker = wecker.wecker()
        self.endtime = False

    def is_locked(self, filepath):
        """Checks if a file is locked by opening it in append mode.
        If no exception thrown, then the file is not locked.
        """
        locked = None
        file_object = None
        if os.path.exists(filepath):
            try:
                buffer_size = 8
                # Opening file in append mode and read the first 8 characters.
                file_object = open(filepath, 'a', buffer_size)
                if file_object:
                    locked = False
            except IOError:
                locked = True
            finally:
                if file_object:
                    file_object.close()
        else:
            print "%s not found." % filepath
        return locked
    
    def run(self):
        #sys.stdout = open('var/www/wecker/log', 'w')
        while True:
            if self.endtime == False:
                # Check alarms
                alarmfile = "var/www/wecker/alarms.xml"
                if not self.is_locked(alarmfile): 
                    self.endtime = self.wecker.checkAlarm()
                    # no alarm to start
                    if self.endtime == False:
                        time.sleep(30)
            else:
                # wait for end of alarm
                if self.endtime != False: 
                    print 'Warte '+str(self.endtime)+' minutes'
                    time.sleep(self.endtime*60)
                    self.wecker.beendeWecker()
                    self.endtime = False
                
          
class Logger(object):
    def __init__(self):
        self.terminal = sys.stdout
        self.log = open("wecker.log", "a")

    def write(self, message):
        self.terminal.write(message)
        self.log.write(message)  

sys.stdout = Logger()            
app = weckerApp()
daemon_runner = runner.DaemonRunner(app)
daemon_runner.do_action()
