<?php
include('lib/powerpi.php');

$data = GetData();

// ### Sockets ###
$sockets = ParseSockets($data);
$socket_table = '';
for ($i = 0; $i < count($sockets); $i++) {
    $socket_table .= "<tr><td>{$sockets[$i]['name']}</td>
                        <td>{$sockets[$i]['code']}</td>
                        <td><a socket-name=\"{$sockets[$i]['name']}\" socket-code=\"{$sockets[$i]['code']}\" href=\"#\" class=\"socket_delete btn btn-large btn-block btn-danger\" style=\"float:none;\"><span class=\"fui-cross\"></span></a></td></tr>";
}

// ### GPIO'S ###
$gpios = ParseGpios($data);
$gpio_table = '';
for ($i = 0; $i < count($gpios); $i++) {
    $gpio_table .= "<tr><td>{$gpios[$i]['name']}</td>
                        <td>{$gpios[$i]['gpio']}</td>
                        <td><a gpio-name=\"{$gpios[$i]['name']}\" gpio-id=\"{$gpios[$i]['gpio']}\" href=\"#\" class=\"gpio_delete btn btn-large btn-block btn-danger\" style=\"float:none;\"><span class=\"fui-cross\"></span></a></td></tr>";
}

// ### Scheduler ###
$schedules = ParseSchedules($data);
$schedule_table = '';
for ($i = 0; $i < count($schedules); $i++) {
    $onoff_text = (($schedules[$i]['onoff'] == '1') ? 'ON' : 'OFF');
    $socket_notfound = (($schedules[$i]['socket'] == '' || ArraySearch('name', $schedules[$i]['socket'], $sockets)) ? '' : '<span class="notfound">[Not Found]</span>');
    $gpio_notfound = (($schedules[$i]['gpio'] == '' || ArraySearch('name', $schedules[$i]['gpio'], $gpios)) ? '' : '<span class="notfound">[Not Found]</span>');
    $schedule_table .= "<tr>
													<td>{$schedules[$i]['name']}</td>
													<td>{$schedules[$i]['socket']} $socket_notfound</td>
                          <td>{$schedules[$i]['gpio']} $gpio_notfound</td>
                          <td>" . str_pad($schedules[$i]['hour'], 2, 0, STR_PAD_LEFT) . ":" . str_pad($schedules[$i]['minute'], 2, 0, STR_PAD_LEFT) . "</td>
                          <td>$onoff_text</td>
                        <td><a schedule-name=\"{$schedules[$i]['name']}\" schedule-socket=\"{$schedules[$i]['socket']}\" schedule-gpio=\"{$schedules[$i]['gpio']}\" schedule-hour=\"{$schedules[$i]['hour']}\" schedule-minute=\"{$schedules[$i]['minute']}\" schedule-onoff=\"{$schedules[$i]['onoff']}\" href=\"#\" class=\"schedule_delete btn btn-large btn-block btn-danger\" style=\"float:none;\"><span class=\"fui-cross\"></span></a></td></tr>";
}

$gpio_select = ParseSelect($gpios, 'Choose GPIO');
$socket_select = ParseSelect($sockets, 'Choose Socket');
$hour_select = TimeSelect(23);
$minute_select = TimeSelect(59);
?>
<script>
    $(document).ready(function() {

        // ### Sockets ###
        $('#socket_submit').click(function() {
            var name = $('#socket_name').val();
            var code = $('#socket_code').val();

            if (name == '' || code == '') {
                alert('Please enter name and socket');
                return;
            }

            $.get('lib/powerpi.php?action=addsocket&name=' + name + '&code=' + code, function() {
                window.location.reload();
            });
        });

        $('.socket_delete').click(function() {
            var name = $(this).attr('socket-name');

            if (name == '') {
                alert('Socket information incomplete');
                return;
            }
            if (confirm('Sure?')) {
                $.get('lib/powerpi.php?action=deletesocket&socket=' + name, function() {
                    window.location.reload();
                });
            }
        });

        // ### GPIOS ###
        $('#gpio_submit').click(function() {
            var name = $('#gpio_name').val();
            var gpio = $('#gpio_id option:selected').val();

            if (name == '' || gpio == '') {
                alert('Please enter name and choose gpio');
                return;
            }

            $.get('lib/powerpi.php?action=addgpio&name=' + name + '&gpio=' + gpio, function() {
                window.location.reload();
            });
        });

        $('.gpio_delete').click(function() {
            var name = $(this).attr('gpio-name');
            var id = $(this).attr('gpio-id');

            if (name == '' || id == '') {
                alert('Gpio information incomplete');
                return;
            }
            if (confirm('Sure?')) {
                $.get('lib/powerpi.php?action=deletegpio&gpio=' + name + '&pin=' + id, function() {
                    window.location.reload();
                });
            }
        });

        // ### Scheduler ###
        $('#schedule_submit').click(function() {

            var name = $('#schedule_name').val();
            var socket = $('#schedule_socket option:selected').val();
            var gpio = $('#schedule_gpio option:selected').val();
            var hour = $('#schedule_hour option:selected').val();
            var minute = $('#schedule_minute option:selected').val();
            //var onoff = $('#schedule_onoff option:selected').val();
            var onoff = 0;
            if ($('#schedule_onoff').hasClass('checked')) {
                onoff = 1
            }

            if (name == '') {
                alert('Please enter a name');
                return;
            }

            if (socket == '' && gpio == '') {
                alert('Please choose a socket and/or gpio');
                return;
            }

            $.get('lib/powerpi.php?action=addschedule&name=' + name + '&socket=' + socket + '&gpio=' + gpio + '&hour=' + hour + '&minute=' + minute + '&onoff=' + onoff, function() {
                window.location.reload();
            });
        });

        $('.schedule_delete').click(function() {
            var name = $(this).attr('schedule-name');

            if (name == '') {
                alert('Schedule information incomplete');
                return;
            }
            if (confirm('Sure?')) {
                $.get('lib/powerpi.php?action=deleteschedule&schedule=' + name, function() {
                    window.location.reload();
                });
            }
        });

    });


</script>
<div class="panel panel-default">
    <div class="panel-heading">Wireless Sockets</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped font-big" id="no-more-tables">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code (e.g. 11001A)</th>
                    <th class="span2"></th>
                </tr>
            </thead>
            <tbody>
<?= $socket_table ?>
                <tr> 
                    <td class="add center"><input class="form-control" id="socket_name" type="text" value="" placeholder="Name" class="span3 nomargb fullwidth"></td>
                    <td class="add center"><input class="form-control" id="socket_code" type="text" value="" placeholder="Code (e.g. 11001A)" class="span3 nomargb"></td>
                    <td class="add center"><a id="socket_submit" href="#" class="btn btn-large btn-block btn-primary" style="float:none;">Add</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">GPIO's</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped font-big" id="no-more-tables">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>GPIO</th>
                    <th class="span2"></th>
                </tr>
            </thead>
            <tbody>
<?= $gpio_table ?>
                <tr> 
                    <td class="add center"><input class="form-control" id="gpio_name" type="text" value="" placeholder="Name" class="span3 nomargb fullwidth"></td>
                    <td class="add center">
                        <select class="form-control" name="gpio_id" id="gpio_id">
                            <option value="2">GPIO 2</option>
                            <option value="3">GPIO 3</option>
                            <option value="4">GPIO 4</option>
                            <option value="7">GPIO 7</option>
                            <option value="8">GPIO 8</option>
                            <option value="9">GPIO 9</option>
                            <option value="10">GPIO 10</option>
                            <option value="11">GPIO 11</option>
                            <option value="14">GPIO 14</option>
                            <option value="15">GPIO 15</option>
                            <option value="17">GPIO 17</option>
                            <option value="18">GPIO 18</option>
                            <option value="22">GPIO 22</option>
                            <option value="23">GPIO 23</option>
                            <option value="24">GPIO 24</option>
                            <option value="25">GPIO 25</option>
                            <option value="27">GPIO 27</option>
                            <option value="28">GPIO 28</option>
                            <option value="29">GPIO 29</option>
                            <option value="30">GPIO 30</option>
                            <option value="31">GPIO 31</option>
                        </select>
                    </td>
                    <td class="add center"><a id="gpio_submit" href="#" class="btn btn-large btn-block btn-primary" style="float:none;">Add</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Scheduler</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped font-big" id="no-more-tables">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Socket</th>
                    <th>GPIO</th>
                    <th>Time (Now: <?= exec('date +"%T"') ?>)</th>
                    <th>On/Off</th>
                    <th class="span2"></th>
                </tr>
            </thead>
            <tbody>
<?= $schedule_table ?>
                <tr>
                    <td class="add center">
                        <input class="form-control" id="schedule_name" type="text" value="" placeholder="Name" class="span3 nomargb fullwidth">
                    </td> 
                    <td class="add center">
                        <select class="form-control" id="schedule_socket" class="select-block span3">
<?= $socket_select ?>
                        </select>
                    </td>
                    <td class="add center">
                        <select class="form-control" id="schedule_gpio" class="select-block span3">
<?= $gpio_select ?>
                        </select>
                    </td>
                    <td class="add center">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" id="schedule_hour" class="select-block span2">
<?= $hour_select ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="schedule_minute" class="select-block span2">
<?= $minute_select ?>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td class="add center">
                        <label class="checkbox" id="schedule_onoff" for="schedule_status">
                            <input type="checkbox" class="form-control" value="" id="schedule_status" data-toggle="checkbox">ON</label>
                            <!--<select id="schedule_onoff" class="select-block span2">
                              <option value="1">ON</option>
                              <option value="0">OFF</option>
                            <select>-->
                    </td>
                    <td class="add center"><a id="schedule_submit" href="#" class="btn btn-large btn-block btn-primary" style="float:none;">Add</a></td>
                </tr>
            </tbody>
        </table>
    </div
</div>