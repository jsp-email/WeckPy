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
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-4 col-sm-6 col-md-4 col-lg-2">
                 Alarms
              </div>
            <div class="col-xs-8 col-sm-6 col-md-8 col-lg-10">
              <div class="pull-right" style="margin-right:10px;"><span class="hidden-xs">Last Check: <?= file_get_contents("last_update.txt") ?> (Now: <?= date('H:i', strtotime('now'))?>)</span><span class="visible-xs"><?= file_get_contents("last_update.txt") ?> (<?= date('H:i', strtotime('now'))?>)</span></div>
            </div>
          </div>
        </div>
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
