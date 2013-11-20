<script>
    $(document).ready(function() {
        $('.wecker').click(function() {
            var wecker = $(this).attr('wecker-id');
            if (wecker != '' && wecker != 'undefined') {
                $.get('./lib/weckerpy.php?action=changestatus&wecker=' + wecker, function(data) {
                    if (data != 'True') {
                        alert(data);
                    }
                    window.location.reload();
                });
            }
        });
    });
</script>
<?php
include_once('lib/weckerpy.php');
$alarms = getWecker();

if (!empty($alarms)) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">Alarms</div>
        <div class="panel-body">
            <ul class="buttonlist">

    <?php foreach ($alarms as $al) { ?>
                    <li>
                        <div class="button wecker <?= $al['Active'] == 'OFF' ? 'disabled' : '' ?> " wecker-id="<?= $al['id'] ?>">
                            <div class="button_text"><?= implode($al['weekdays'], ', ') ?></div>
                            <div class="button_time"><?= $al['uhrzeit'] ?></div>
                            <div class="status"><?= $al['Active'] ?></div>
                        </div>
                    </li>              

    <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>
