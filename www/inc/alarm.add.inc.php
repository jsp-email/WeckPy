<div class="panel panel-default">
    <div class="panel-heading">Alarm Settings</div>
    <div class="panel-body">
<?php
include_once('lib/weckerpy.php');
?>
  <script>
    $(document).ready(function() {
        $('#fade_div').hide();
        
        $('#wecker_fade').click(function(event){
            $('#fade_div').toggle();
        });
    
        $("#addform").submit(function(e){
            e.preventDefault();
            
            var weekday = $('#wecker_wochentag').val();
            var hour = $('#wecker_hour option:selected').val();
            var minute = $('#wecker_minute option:selected').val();
            var playlist = $('#wecker_playlist option:selected').val();
            var duration = $('#wecker_duration').val();

            var onoff = 0;
            if ($('#wecker_onoff').hasClass('checked')) {
                onoff = 1
            }
            var fade = 0;
            if ($('#wecker_fade').hasClass('checked')) {
                fade = 1
            }
            var power_on = $('#wecker_power_on').val();
            var power_off = $('#wecker_power_off').val();
            var end_volume = $('#wecker_end_volume').val();
            var start_volume = $('#wecker_start_volume').val();
            var tts_content = $('#wecker_tts').val();
            var tts = $('<div/>').text(tts_content).html();

            if (weekday == null) {
                alert('Please choose a weekday');
                return;
            }
            if (duration == '') {
                duration = 0;
            }

            if (playlist == '') {
                alert('Please choose a playlist');
                return;
            }

            $.get('./lib/weckerpy.php?action=addwecker&weekday=' + weekday + '&hour=' + hour + '&minute=' + minute + '&playlist=' + playlist + '&fade=' + fade + '&duration=' + duration + '&onoff=' + onoff + '&power-on=' + power_on + '&power-off=' + power_off + '&start-volume=' + start_volume + '&end-volume=' + end_volume + '&tts='+ tts, function(data) {
                if (data != 'True') {
                    alert(data);
                }
                window.location.href = "index.php?action=alarmsettings";
            });
          return false;
      });
      
    });
                
</script>
     <form role="form" id="addform">
            <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Weekday</label>                    
                        <select class="form-control" id="wecker_wochentag" class="select-block" multiple>
                            <?php
                            $timestamp = strtotime('today');
                            $days = array();
                            for ($i = 0; $i < 7; $i++) {
                                $days[] = strftime('%A', $timestamp);
                                $timestamp = strtotime('+1 day', $timestamp);
                            }
                            foreach ($days as $d) {
                                echo '<option value="' . $d . '">' . $d . '</option>';
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Time</label>                        
                        <div class="row">
                          <div class="col-xs-6">
                              <select class="form-control" id="wecker_hour" >
                                  <option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
                              </select>
                          </div>
                          <div class="col-xs-6">
                              <select class="form-control" id="wecker_minute" >
                                  <option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                              </select>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="wecker_playlist">Playlist</label>
                        <select class="form-control" id="wecker_playlist" class="select-block">
                            <option value="">Choose Playlist</option>
                            <option value="wecker">Wecker</option>
                            <option value="housetime">Housetime</option>
                            <option value="housetime">Top100</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="wecker_duration">Duration <span style="color: #737373; font-size: 12px">Zero equals endless alarm</span></label>
                        <input class="form-control" id="wecker_duration" type="number" size="1" placeholder="Duration" class="nomargb">
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label" for="wecker_start_volume">Start Volume</label>
                        <input class="form-control" id="wecker_start_volume" type="number" size="1"  value="30" class="nomargb">
                    </div>
                    <div class="checkbox" id="wecker_fade">
                         <label for="wecker_fadestatus">
                            <input type="checkbox" class="form-control" value="" id="wecker_fadestatus" data-toggle="checkbox">Fade Music In</label>
                    </div>
                    <div id="fade_div">
                      <div class="form-group">
                          <label class="control-label" for="wecker_end_volume">End Volume</label>
                          <input class="form-control" id="wecker_end_volume" type="number" size="1"  value="45" class="nomargb">
                      </div>
                    </div>
                      
                    
          </div>
          <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Power Supplies ON</label>                    
                        <select class="form-control" id="wecker_power_on" class="select-block" multiple>
                            <?php
                            include_once('lib/powerpi.php');
                            $data = GetData();
                            $sockets = ParseSockets($data);
                            foreach ($sockets as $socket) {
                              if($socket['name'] == 'Verstaerker')
                                echo '<option selected value="' . urlencode($socket['name']) . '">' . $socket['name'] . '</option>';
                              else
                                echo '<option value="' . urlencode($socket['name']) . '">' . $socket['name'] . '</option>';
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Power Supplies OFF</label>                    
                        <select class="form-control" id="wecker_power_off" class="select-block" multiple>
                            <?php
                            include_once('lib/powerpi.php');
                            $data = GetData();
                            $sockets = ParseSockets($data);
                            foreach ($sockets as $socket) {
                                echo '<option value="' . urlencode($socket['name']) . '">' . $socket['name'] . '</option>';
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="checkbox" id="wecker_onoff" >
                         <label for="wecker_status">
                            <input type="checkbox" class="form-control" value="" id="wecker_status" data-toggle="checkbox">Active</label>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="wecker_tts">TTS</label>
                        <textarea class="form-control" id="wecker_tts" rows="3" placeholder="Text to say"></textarea>
                        <p class="help-block">The following Placeholder are possible:
                          <ul class="help-block">
                            <li>{time} for the current Time</li>
                            <li>{temp} for the current temperature</li>
                            <li>{wetter} for the current weather</li>
                          </ul>
                        </p>
                    </div>
                  
                    <button type="submit" class="btn btn-primary">Create Alarm</button>
                    
          </div>          
        </form>
</div>
</div>