<div class="panel panel-default">
    <div class="panel-heading">Alarm Settings</div>
    <div class="panel-body">
<?php
include_once('lib/weckerpy.php');
?>
<script>
    $(document).ready(function() {

        $('.wecker_delete').click(function() {
            var wecker_id = $(this).attr('wecker-id');

            if (wecker_id == '') {
                alert('wecker information incomplete');
                return;
            }
            if (confirm('Sure?')) {
                $.get('./lib/weckerpy.php?action=deletewecker&wecker=' + wecker_id, function() {
                    window.location.reload();
                });
            }
        });

    });


</script>
        <table class="table table-hover table-striped font-big" id="no-more-tables">
            <thead>
                <tr>
                    <th>Weekday</th>
                    <th>Time</th>
                    <th>Playlist</th>
                    <th>Duration [min]</th>
                    <th>Fade In</th>
                    <th>On/Off</th>
                    <th></th>
                    <th class="span2"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $alarms = getWecker();
                foreach ($alarms as $al) {
                    ?>

                    <tr>
                        <td><?= implode($al['weekdays'], ', ') ?></td>
                        <td><?= $al['uhrzeit'] ?></td>
                        <td><?= $al['Playlist'] ?></td>
                        <td><?= $al['Dauer']== 0?'endless': $al['Dauer'] ?></td>
                        <td><?= $al['Fade'] ?></td>
                        <td><?= $al['Active'] ?></td>
                        <td><a wecker-id="<?= $al['id'] ?>" href="index.php?action=alarmsettings&a=detail&id=<?= $al['id'] ?>" class="btn btn-large btn-block btn-danger" style="float:none;">Details</a></td>
                        <td><a wecker-id="<?= $al['id'] ?>" href="#" class="wecker_delete btn btn-large btn-block btn-danger" style="float:none;"><span class="fui-cross"></span></a></td>
                    </tr>
                <?php } ?> 
               <tr>
                  <td colspan="7"></td>
                  <td><a id="wecker_submit" href="?action=alarmsettings&a=add" class="btn btn-large btn-block btn-primary">Add</a></td>
               <tr>               
            </tbody>
        </table>
</div>
</div>
 
</div>
</div>