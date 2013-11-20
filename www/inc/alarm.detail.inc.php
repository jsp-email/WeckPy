<div class="panel panel-default">
    <div class="panel-heading">Alarm Settings</div>
    <div class="panel-body">
<?php
include_once('lib/weckerpy.php');
 $id = Get('id');
 $alarms = getWeckerfromID($id);
 if(!empty($alarms)){
 ?>
 <div class="row">
            <div class="col-md-6">
                <h4>Weekday</h4>
                <p><?= implode('<br/>', $alarms['weekdays'])?></p>
                 
                 <h4>Time</h4>
                 <p><?= $alarms['uhrzeit'] ?></p>
                 
                 <h4>Playlist</h4>
                 <p><?= $alarms['Playlist'] ?></p>
                 
                 <h4>Duration</h4>
                 <p><?= $alarms['Dauer'] ?></p>
                                 
                 <h4>Fade In</h4>
                 <p><?= $alarms['Fade'] == 1? 'ON': 'OFF' ?></p>
                 
                 <h4>Start Volume</h4>
                 <p><?= $alarms['startVol'] ?>%</p>
                 
                 <h4>End Volume</h4>
                 <p><?= $alarms['endVol'] ?>%</p>
                        
          </div>
          <div class="col-md-6">
              <h4>Power Supplies ON</h4>
              <p><?= implode('<br/>', $alarms['power-on'])?></p>
              
              <h4>Power Supplies OFF</h4>
              <p><?= implode('<br/>', $alarms['power-off'])?></p>
              
              <h4>Active</h4>
              <p><?= $alarms['Active'] ?></p>
              
              <?php if(array_key_exists('tts', $alarms)){ ?>
                <h4>TTS</h4>
                <p><?= $alarms['tts'] ?></p>
              <?php } ?>                    
          </div>          
        </div>
 <?php
  }
?>

</div>
</div>