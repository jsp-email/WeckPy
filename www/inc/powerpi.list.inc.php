<script>
    $(document).ready(function() {
        $('.socket > .button_on').click(function() {
            var socket = $(this).attr('socket-name');
            if (socket != '' && socket != 'undefined') {
                $.get('lib/powerpi.php?action=setsocket&socket=' + socket + '&status=1', function() {
                });
            }
        });

        $('.socket > .button_off').click(function() {
            var socket = $(this).attr('socket-name');
            if (socket != '' && socket != 'undefined') {
                $.get('lib/powerpi.php?action=setsocket&socket=' + socket + '&status=0', function() {
                });
            }
        });

        $('.gpio > .button_on').click(function() {
            var gpio = $(this).attr('gpio-name');
            if (gpio != '' && gpio != 'undefined') {
                $.get('lib/powerpi.php?action=setgpio&gpio=' + gpio + '&status=1', function() {
                });
            }
        });

        $('.gpio > .button_off').click(function() {
            var gpio = $(this).attr('gpio-name');
            if (gpio != '' && gpio != 'undefined') {
                $.get('lib/powerpi.php?action=setgpio&gpio=' + gpio + '&status=0', function() {
                });
            }
        });

        $('.schedule').click(function() {
            var schedule = $(this).attr('schedule-name');
            if (schedule != '' && schedule != 'undefined') {
                $.get('lib/powerpi.php?action=setschedule&schedule=' + schedule, function() {
                    window.location.reload();
                });
            }
        });
        $('.button_on.all').click(function() {
            $.get('lib/powerpi.php?action=setsocket&status=1', function(data) {
                   
                });
        });
        $('.button_off.all').click(function() {
            $.get('lib/powerpi.php?action=setsocket&status=0', function(data) {
                    
                });
        });
    });
</script>
<?php
include_once('lib/powerpi.php');

$data = GetData();

// ### Sockets ###
$sockets = ParseSockets($data);
$sockets_out = '';
for ($i = 0; $i < count($sockets); $i++) {
    $sockets_out .= "<li>
                      <div class=\"button socket\">
                        <div class=\"button_text\">{$sockets[$i]['name']}</div>
                        <div class=\"button_off button\" socket-name=\"{$sockets[$i]['name']}\">OFF</div>
                        <div class=\"button_on button\" socket-name=\"{$sockets[$i]['name']}\">ON</div> 
                      </div>
                     </li>";
                     
              
}
$sockets_out = ((count($sockets) > 0) ? "<ul class=\"buttonlist\">{$sockets_out}</ul>" : '');

if ($sockets_out != '') {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-2">
               Sockets
            </div>
          <div class="col-xs-8 col-sm-8 col-md-8 col-lg-10">
            <div class="pull-right" style="margin-right:10px;">  
              <div class="button_off button all">OFF</div>
              <div class="button_on button all">ON</div> 
            </div>
          </div>
        </div>
        
        </div>
        <div class="panel-body">
    <?= $sockets_out ?>

        </div>
    </div>
<?php
}
// ### GPIO'S ###
$gpios = ParseGpios($data);
$gpios_out = '';
for ($i = 0; $i < count($gpios); $i++) {
    $gpios_out .= "<li>
                          <div class=\"button gpio\">
                              <div class=\"button_text\">{$gpios[$i]['name']}</div>
                              <div class=\"button_off button\" gpio-name=\"{$gpios[$i]['name']}\">OFF</div>
                              <div class=\"button_on button\" gpio-name=\"{$gpios[$i]['name']}\">ON</div> 
                            </div>
                          </li>";
}
$gpios_out = ((count($gpios) > 0) ? "<ul class=\"buttonlist\">{$gpios_out}</ul>" : '');

if ($gpios_out != '') {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">GPIOs</div>
        <div class="panel-body">
    <?= $gpios_out ?>

        </div>
    </div>
<?php
}
// ### Schedules ###
$schedules = ParseSchedules($data);
$schedules_out = '';
for ($i = 0; $i < count($schedules); $i++) {

    $status_text = (($schedules[$i]['status'] == '1') ? 'Active' : 'Inactive');

    $schedules_out .= "<li>
                          <div class=\"button schedule";
    $schedules_out .= (($schedules[$i]['status'] == '1') ? '' : ' disabled');
    $schedules_out .= "\" schedule-name=\"{$schedules[$i]['name']}\">
                              <div class=\"button_text\">{$schedules[$i]['socket']}</div>
                              <div class=\"button_time\">{$schedules[$i]['hour']}:{$schedules[$i]['minute']}</div>
                              <div class=\"status\">$status_text</div>
                            </div>
                          </li>";
}
$schedules_out = ((count($schedules) > 0) ? "<ul class=\"buttonlist\">{$schedules_out}</ul>" : '');
?>
        <?php if ($schedules_out != '') { ?>
    <div class="panel panel-default">
        <div class="panel-heading">Schedules</div>
        <div class="panel-body">
    <?= $schedules_out ?>

        </div>
    </div>
<?php } ?>