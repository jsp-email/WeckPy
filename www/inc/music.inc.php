<?php

function convertSecs($secs) {
    if ($secs < 0)
        return false;

    $m = (int) ($secs / 60);
    $s = $secs % 60;

    $h = (int) ($m / 60);
    $m = $m % 60;

    if($h < 10)
      $h = '0'.$h;
    if($m < 10)
      $m = '0'.$m;
    if($s < 10)
      $s = '0'.$s;
      
    if ($h == '00')
      $h = 0;
    return array($h, $m, $s);
}

/* Settings */
$mpdServer = 'localhost';
$mpdPort = '6600';
$mpdPassword = NULL;

/* Do not change */
ob_start();

include 'lib/mpd.class.php';

$mpd = new mpd($mpdServer, $mpdPort, $mpdPassword);


define('CURRENTARTIST', $mpd->playlist[$mpd->current_track_id]['Artist']);
define('CURRENTTRACK', $mpd->playlist[$mpd->current_track_id]['Title'] != NULL ?  $mpd->playlist[$mpd->current_track_id]['Title'] : end(explode( '/', $mpd->playlist[$mpd->current_track_id]['file'])) );          
define('CURRENTID', $mpd->playlist[$mpd->current_track_id]['Id']);


if (isset($_POST['toadd'])) {
    $object = $_POST['toadd'];

    $files = explode("\n", $mpd->SendCommand('lsinfo'));

    foreach ($files as $row) {
        $file = explode(':', $row);
        $thefiles[][$file[0]] = ltrim($file[1]);
    }

    foreach ($thefiles as $search) {
        if (array_search($object, $search) == 'directory') {
            $dir = $mpd->GetDir($object);

            foreach ($dir as $addRow) {
                $addArr[] = $addRow['file'];
            }

            $mpd->PLAddBulk($addArr);
            break;
        } else {
            $songs = explode(',', $object);

            $mpd->PLAddBulk($songs);
            break;
        }
    }
    $mpd->SendCommand('update');
    header('Location: ./index.php?action=music');
}
?>

<script>

    var VOLDOWNSTEPS = 2;
    var VOLUPSTEPS = 2;
    

    $(document).ready(function() {
    
        $('.start').each(function(i, obj) {
            $(".remove:eq("+i+")").css("height",$( this ).height()+10);
            $(".remove:eq("+i+")").css('padding-top', ($(this).height()-10)/2);
        });
      
      
        $("#volUp").click(function(event) {
            $.get('lib/music.php?a=volup');
            event.preventDefault();

            $("#outVolup").fadeIn('slow', function() {
                $("#outVolup").fadeOut('slow');
            });

            var currentvol = parseInt($("#currentvol").text());
            $("#currentvol").text(currentvol + VOLUPSTEPS);

        });

        $("#volDown").click(function(event) {
            $.get('lib/music.php?a=voldown');
            event.preventDefault();

            $("#outVoldown").fadeIn('slow', function() {
                $("#outVoldown").fadeOut('slow');
            });

            var currentvol = parseInt($("#currentvol").text());
            $("#currentvol").text(currentvol - VOLDOWNSTEPS);
        });

        $("#prev").click(function(event) {
            $.get('lib/music.php?a=prev', function() {
                window.location.reload();
            });
            event.preventDefault();

            $("#outPrev").fadeIn('slow', function() {
                $("#outPrev").fadeOut('slow');
            });

        });
        $("#next").click(function(event) {
            $.get('lib/music.php?a=next', function() {
                window.location.reload();
            });
            event.preventDefault();

            $("#outNext").fadeIn('slow', function() {
                $("#outNext").fadeOut('slow');
            });
        });
        $("#play").click(function(event) {
            $.get('lib/music.php?a=play', function() {
                window.location.reload();
            });
            event.preventDefault();
            $("#outPlay").fadeIn('slow', function() {
                $("#outPlay").fadeOut('slow');
            });
        });
        $("#pause").click(function(event) {
            $.get('lib/music.php?a=pause', function() {
                window.location.reload();
            });
            event.preventDefault();
            $("#outPause").fadeIn('slow', function() {
                $("#outPause").fadeOut('slow');
            });
        });
        $("#stop").click(function(event) {
            $.get('lib/music.php?a=stop', function() {
                window.location.reload();
            });
            event.preventDefault();
        });

        $(".start").click(function(event) {
            var startid = $(this).attr('start-id');
            $.get('lib/music.php?a=start&id=' + startid, function() {
                window.location.reload();
            });
            event.preventDefault();
        });
        
        $(".remove").click(function(event) {
            var startid = $(this).attr('remove-id');
            $.get('lib/music.php?a=remove&id=' + startid, function() {
                window.location.reload();
            });
            event.preventDefault();
        });

        $("#clearpl").click(function(event) {

            $.get('lib/music.php?a=clearpl', function() {
                window.location.reload();
            });
            event.preventDefault();

        });

    });
    
    /*
 * Window resize Handling
 */
$(window).resize(function() {
    $('.start').each(function(i, obj) {
            $(".remove:eq("+i+")").css("height",$( this ).height()+10);
            $(".remove:eq("+i+")").css('padding-top', ($(this).height()-10)/2);
        });
});
</script>
<?php
if ($mpd->connected == FALSE) {
    echo "Error: " . $mpd->errStr;
} else {
    $statusrow = explode("\n", $mpd->SendCommand('status'));

    foreach ($statusrow as $row) {
        $get = explode(': ', $row);
        $status[$get[0]] = $get[1];
    }

    $times = explode(':', $status['time']);
    $CURRENTLENGTH = convertSecs($times[1]);
    $CURRENTTIME = convertSecs($times[0]);
    

    // fucking dirty
    if ($mpd->state != 'stop') {
        $refresh = ((($times[1] - $times[0]) * 1000) + 500);
        if ($refresh < 1) {
            $refresh = 30500;
        }
        echo '<script type="text/javascript">setTimeout("location.reload(true);", ' . $refresh . ');</script>' . "\n";
    }

    switch ($mpd->state) {
        case 'play':
            $status = 'playing';
            break;
        case 'pause':
            $status = 'paused';
            break;
        default:
            $status = 'stopped';
            break;
    }
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
        <div class="row">
          <div class="col-sm-6 col-md-6 col-lg-7">
            <?php
            if ($status != 'stopped') {
                if ($status != 'paused') {
                    ?>
                    <script type="text/javascript">
                        var min = <?php echo date('i', $times[0]) ?>;
                        var sec = <?php echo date('s', $times[0]) ?>;
                        function zeropad(n, digits) {
                            n = n.toString();
                            while (n.length < digits) {
                                n = '0' + n;
                            }
                            return n;
                        }
                        function cnt() {
                            sec++;
                            if (sec > 59) {
                                min++;
                                sec = 0;
                            }
                            document.getElementById('time').innerHTML = zeropad(min, 2) + ":" + zeropad(sec, 2);
                        }
                        window.setInterval(cnt, 1000);
                    </script>
                <?php } ?> 
                <?= CURRENTARTIST != NULL ? CURRENTARTIST.'-' : '' ?>  <?php echo CURRENTTRACK ?> (<span id="time"><? if($status == 'paused') {  echo ($CURRENTTIME[0] ? $CURRENTTIME[0] . ':' : '') . $CURRENTTIME[1] . ':' . $CURRENTTIME[2];} ?></span>
                <?php if ($CURRENTLENGTH[0] + $CURRENTLENGTH[1] + $CURRENTLENGTH[2] > 0): ?>/<?php echo ($CURRENTLENGTH[0] ? $CURRENTLENGTH[0] . ':' : '') . $CURRENTLENGTH[1] . ':' . $CURRENTLENGTH[2]; ?><?php endif; ?>)

            <?php
            }else {
                echo "Music Control";
            }
            ?>
              </div>
            <div class="col-sm-6 col-md-6 col-lg-5">
            <div class="pull-right">
                <span class="ajaxOutput">
                    <span id="outVolup">Vol ++</span>
                    <span id="outVoldown">Vol --</span>
                    <span id="outNext">Next song</span>
                    <span id="outPrev">Previous song</span>
                    <span id="outPause">Paused</span>
                    <span id="outPlay">Play</span>
                </span>
                Status: <?php if ($status != 'stopped'): ?><a href="#current"><?php echo $status ?></a><?php else: echo $status;
            endif;
            ?> | Songs: <?php echo $mpd->playlist_count ?> | Vol: <span id="currentvol"><?php echo $mpd->volume ?></span>%
            </div>
            </div>
        </div>
        </div>
        
        <div class="panel-body">
          <div class="row">
            <div class="btn-toolbar pull-right" role="navigation">
                <div class="btn-group">
                    <button type="button" id="volDown" title="Vol down" class="btn btn-default"><img src="images/sound_down.png" alt="Vol down" /></button>
                    <button type="button" id="volUp" title="Vol up" class="btn btn-default"><img src="images/sound_up.png" alt="Vol up" /></button>
                </div>
                <div class="btn-group">
                    <button type="button" id="prev" title="Previous" class="btn btn-default"><img src="images/control_rewind.png" alt="Previous" /></button>
                    <?php if ($mpd->state == 'pause' || $mpd->state == 'stop'): ?>
                        <button type="button" id="play" title="Play" class="btn btn-default"><img src="images/control_play.png" alt="Play" /></button>
    <?php else: ?>
                        <button type="button" id="pause" title="Pause" class="btn btn-default"><img src="images/control_pause.png" alt="Pause" /></button>
    <?php endif; ?>
                    <button type="button" id="stop" title="Stop" class="btn btn-default"><img src="images/control_stop.png" alt="Stop" /></button>
                    <button type="button" id="next" title="Next" class="btn btn-default"><img src="images/control_fastforward.png" alt="Fastforward" /></button>
                </div>
            </div>
          </div> 
            <div class="row">
            <!--<div class="well">-->
                <!--<div class="panel-body">-->
                    <div class="playlist">

                        <?php
                        $titlelist = '';
                        $deletelist = '';
                        foreach ($mpd->playlist as $song) {
                            
                            if ($song['Artist'] != NULL && $song['Title'] != NULL) {
                                $sngtm = convertSecs($song['Time']);
                                $songtime = ($sngtm[0] ? $sngtm[0] . ':' : '') . $sngtm[1] . ':' . $sngtm[2];
                                $titlelist .= '<a href="#" start-id="' . $song['Pos'] . '" class="start list-group-item ';
                                if (CURRENTID == $song['Id']){
                                  $titlelist .= 'active" name="current"';
                                }
                                $titlelist .= '">';
                                if($song['Time'] != NULL){
                                  $sngtm = convertSecs($song['Time']);
                                  $songtime = ($sngtm[0] ? $sngtm[0] . ':' : '') . $sngtm[1] . ':' . $sngtm[2];
                                  $titlelist .= '<span class="badge">' . $songtime . '</span>';
                                }
                                $titlelist .= $song['Artist'] . ' - ' . $song['Title'] . '</a>';
                                
                                
                            } elseif ($song['Name'] != NULL) {
                                $titlelist .= '<a href="#" start-id="' . $song['Pos'] . '" class="start list-group-item">' . $song['Name'] . '</a>';
                            
                            } elseif ($song['file'] != NULL) {
                              
                                $titlelist .= '<a href="#" start-id="' . $song['Pos'] . '" class="start list-group-item ';
                                
                                if (CURRENTID == $song['Id']){
                                      $titlelist .= 'active" name="current"';
                                }
                                $titlelist .= '">';
                                if($song['Time'] != NULL){
                                  $sngtm = convertSecs($song['Time']);
                                  $songtime = ($sngtm[0] ? $sngtm[0] . ':' : '') . $sngtm[1] . ':' . $sngtm[2];
                                  $titlelist .= '<span class="badge">' . $songtime . '</span>';
                                }
              
                                $titlelist .= end(explode( '/', $song['file'])) . '</a>';
                            }
                            $deletelist .= '<li class="list-group-item"><a href="#" remove-id="' . $song['Id'] . '"  class="remove fui-cross" title="Remove this song"></a></li>';

                        }
                        ?>
                        <div class="col-xs-8 col-sm-10 col-md-11">
                          <ul class="list-group">
                          <?= $titlelist; ?>       
                          </ul>
                        </div>
                        <div class="col-xs-4 col-sm-2 col-md-1">
                            <ul class="list-group">
                            <?= $deletelist; ?> 
                              </ul>
                        </div>


                    </div>
                <!--</div>-->
            <!--</div>-->
        </div>
        </div>
        <div class="panel-footer">
            <div class="btn-toolbar pull-right">
                <button id="clearpl" title="Clear playlist" type="button" class="btn btn-default"><img src="images/table_row_delete.png" alt="Clear Playlist" /></button>
            </div>
            <div class="">
                <form action="./index.php?action=music&a=toadd" method="post">
                    <input class="form-control" type="text" style="width: 150px;" name="toadd" placeholder="Add dir or songs" /> 

                </form>
            </div>
        </div>
    </div>


<?php } ?>