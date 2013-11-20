import wecker
import os
import time
from daemon import runner
from datetime import datetime
import sys
import logging


class weckerApp():

    def __init__(self):
        self.stdin_path = '/dev/null'
        self.stdout_path = '/dev/null'
        self.stderr_path = '/dev/null'
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
            logger.debug("%s not found." % filepath)
        return locked
    
    def run(self):
        while True:
            if self.endtime == False:
                logger.debug("Check Alarm")
                # Check alarms
                alarmfile = "var/www/wecker/alarms.xml"
                if not self.is_locked(alarmfile): 
                    self.endtime = self.wecker.checkAlarm(logger)
                
                # no alarm to start
                if self.endtime == False:
                    time.sleep(30)
                # endless
                if self.endtime == 0:
                    time.sleep(61)
            else:
                logger.info("Alarm active "+self.endtime)
                # wait for end of alarm
                if self.endtime != False:
                    logger.info("Warte "+str(self.endtime)+" minutes") 
                    print 'Warte '+str(self.endtime)+' minutes'
                    time.sleep(self.endtime*60)
                    self.wecker.beendeWecker()
                    self.endtime = False
                
          
app = weckerApp()

logger = logging.getLogger("DaemonLog")
logger.setLevel(logging.DEBUG)
formatter = logging.Formatter("%(asctime)s - %(name)s - %(levelname)s - %(message)s")
handler = logging.FileHandler("/root/weckerPy/weckerApp.log")
handler.setFormatter(formatter)
logger.addHandler(handler)

daemon_runner = runner.DaemonRunner(app)
#This ensures that the logger file handle does not get closed during daemonization
daemon_runner.daemon_context.files_preserve=[handler.stream]
daemon_runner.do_action()