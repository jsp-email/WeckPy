<?php

$file = "/var/www/wecker/alarms.xml";

$action = Get('action');

switch ($action) {

    /* ------------------- Schedule ------------------- */

    case 'addwecker':
        $weekday = Get('weekday');
        $hour = Get('hour');
        $minute = Get('minute');
        $playlist = Get('playlist');
        $duration = Get('duration');
        $onoff = Get('onoff');
        $fade = Get('fade');
        $power_on = Get('power-on');
        $power_off = Get('power-off');
        $start_volume = Get('start-volume');
        $end_volume = Get('end-volume');
        $tts = Get('tts');

        $weekdays = explode(',', $weekday);
        $power_supplies_on = explode(',', $power_on);
        $power_supplies_off = explode(',', $power_off);
        
        $tts = html_entity_decode($tts);

        if ($weekdays != '' && $playlist != ''  && $hour != ''  && $minute != '' && $onoff != '' && $duration != '' && $fade != '' && $start_volume != ''&& $end_volume != '') {
            
            $xmldoc = new DOMDocument();
            if ($xml = file_get_contents($file)) {
                $xmldoc->loadXML($xml, LIBXML_NOBLANKS);
                $xml_root = $xmldoc->getElementsByTagName('root')->item(0);
            } else {
                $xml_root = $xmldoc->createElement("root");
            }
            addElement($xmldoc, $xml_root, $weekdays, $hour, $minute, $duration, $playlist, $onoff, $fade, $power_supplies_on, $power_supplies_off, $start_volume, $end_volume, $tts);
            $fp = fopen($file, "w");
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                $xmldoc->save($file);
                flock($fp, LOCK_UN);
                echo "True";
            } else {
                print "Could not get lock!\n";
            }
        }else{
              print 'Please insert all neccesary Values!\n';
        }
        

        break;

    case 'deletewecker':
        $ID = Get('wecker');

        if ($ID != '') {
            $doc = new DOMDocument();
            $doc->load($file);
            $xpath = new DOMXpath($doc);
            $nodeList = $xpath->query('//Wecker[@id="' . (int) $ID . '"]');
            foreach ($nodeList as $element) {
                $element->parentNode->removeChild($element);
            }
            $fp = fopen($file, "w");
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                $doc->save($file);
                flock($fp, LOCK_UN);
                echo "True";
            } else {
                print "Could not get lock!\n";
            }
        }
        break;

    case 'changestatus':
        $ID = Get('wecker');
        if ($ID != '') {
            $doc = new DOMDocument();
            $doc->load($file);
            $xpath = new DOMXpath($doc);
            $nodeList = $xpath->query('//Wecker[@id="' . (int) $ID . '"]');
            foreach ($nodeList as $element) {
                $element->getElementsByTagName("Active")->item(0)->nodeValue = $element->getElementsByTagName("Active")->item(0)->nodeValue == 1 ? 0 : 1;
            }

            $fp = fopen($file, "w");
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                $doc->save($file);
                flock($fp, LOCK_UN);
                echo "True";
            } else {
                print "Could not get lock!\n";
            }
        }
        break;
}

/* ===================== Functions ===================== */

function addElement(&$xmldoc, &$xml_root, $weekdays, $hour, $minute, $duration, $playlist, $onoff, $fade, $power_supplies_on, $power_supplies_off, $start_volume, $end_volume, $tts) {

    $xml_wecker = $xmldoc->createElement("Wecker");
    // unique ID
    $xml_wecker_id = $xmldoc->createAttribute('id');
    $xml_wecker_id->value = time();
    $xml_wecker->appendChild($xml_wecker_id);
    
    // Weekdays
    $xml_weekdays = $xmldoc->createElement("Weekdays");
    foreach ($weekdays as $day) {
        $xml_weekday = $xmldoc->createElement("Day");
        $xml_day_name = $xmldoc->createAttribute('name');
        $xml_day_name->value = $day;
        $xml_weekday->appendChild($xml_day_name);
        $xml_weekdays->appendChild($xml_weekday);
    }
    $xml_wecker->appendChild($xml_weekdays);
    
    // Time
    $xml_time = $xmldoc->createElement("Uhrzeit", "$hour:$minute");
    $xml_wecker->appendChild($xml_time);
    //Duration
    $xml_duration = $xmldoc->createElement("Dauer", $duration);
    $xml_wecker->appendChild($xml_duration);
    // Playlist
    $xml_playlist = $xmldoc->createElement("Playlist", $playlist);
    $xml_wecker->appendChild($xml_playlist);
    // Active
    $xml_active = $xmldoc->createElement("Active", $onoff);
    $xml_wecker->appendChild($xml_active);
    
    // Fade
    $xml_fade = $xmldoc->createElement("Fade", $fade);
    $xml_wecker->appendChild($xml_fade);
   
    // Start Volume
    $xml_startVol = $xmldoc->createElement("startVol", $start_volume);
    $xml_wecker->appendChild($xml_startVol);
    
    // End Volume
    $xml_endVol = $xmldoc->createElement("endVol", $end_volume);
    $xml_wecker->appendChild($xml_endVol);
    
    
    // TSS Volume
    if($tts != ''){
      $xml_tts = $xmldoc->createElement("TTS", $tts);
      $xml_wecker->appendChild($xml_tts);
    }
   
    // Power ON Supplies
    $xml_power_on = $xmldoc->createElement("Power-On");
    foreach ($power_supplies_on as $supply_on) {
        $xml_supply_on = $xmldoc->createElement("Supply");
        $xml_supply_on_name = $xmldoc->createAttribute('name');
        $xml_supply_on_name->value = $supply_on;
        $xml_supply_on->appendChild($xml_supply_on_name);
        $xml_power_on->appendChild($xml_supply_on);
    }
    $xml_wecker->appendChild($xml_power_on);
    
    // Power OFF Supplies
    $xml_power_off = $xmldoc->createElement("Power-Off");
    foreach ($power_supplies_off as $supply_off) {
        $xml_supply_off = $xmldoc->createElement("Supply");
        $xml_supply_off_name = $xmldoc->createAttribute('name');
        $xml_supply_off_name->value = $supply_off;
        $xml_supply_off->appendChild($xml_supply_off_name);
        $xml_power_off->appendChild($xml_supply_off);
    }
    $xml_wecker->appendChild($xml_power_off);
     
    
    
    $xml_root->appendChild($xml_wecker);
    $xmldoc->appendChild($xml_root);
}

function getWecker() {
    $file = "/var/www/wecker/alarms.xml";
    
    $doc = new DOMDocument();
    $doc->load($file);
    $xml_root = $doc->getElementsByTagName('Wecker');
    $alarms = array();
    foreach ($xml_root as $wecker) {
        $al = array();
        
        $wochentage = $wecker->getElementsByTagName("Weekdays");
        $weekdays_arr = array();
        foreach ($wochentage as $node) {
            if ($node->childNodes->length) {
                foreach ($node->childNodes as $child) {
                    $timestamp = strtotime($child->getAttribute('name'));
                    array_push($weekdays_arr, strftime('%a', $timestamp));
                }
            }
        }
        $al['weekdays'] = $weekdays_arr;
        
        $al['id'] = $wecker->getAttribute('id');
        $al['uhrzeit'] = $wecker->getElementsByTagName("Uhrzeit")->item(0)->nodeValue;
        $al['Playlist'] = $wecker->getElementsByTagName("Playlist")->item(0)->nodeValue;
        $al['Dauer'] = $wecker->getElementsByTagName("Dauer")->item(0)->nodeValue;
        $al['Active'] = $wecker->getElementsByTagName("Active")->item(0)->nodeValue == 1 ? 'ON' : 'OFF';
        $al['Fade'] = $wecker->getElementsByTagName("Fade")->item(0)->nodeValue;
        $al['startVol'] = $wecker->getElementsByTagName("startVol")->item(0)->nodeValue;
        $al['endVol'] = $wecker->getElementsByTagName("endVol")->item(0)->nodeValue;
        
        if($wecker->getElementsByTagName("TTS")){
          $al['tts'] = $wecker->getElementsByTagName("TTS")->item(0)->nodeValue;
        }
        
        $power_supplies_on = $wecker->getElementsByTagName("Power-On");
        $supplies_on_arr = array();
        foreach ($power_supplies_on as $node) {
            if ($node->childNodes->length) {
                foreach ($node->childNodes as $child) {
                    array_push($supplies_on_arr, $child->getAttribute('name'));
                }
            }
        }
        $al['power-on'] = $supplies_on_arr;
        
        $power_supplies_off = $wecker->getElementsByTagName("Power-Off");
        $supplies_off_arr = array();
        foreach ($power_supplies_off as $node) {
            if ($node->childNodes->length) {
                foreach ($node->childNodes as $child) {
                    array_push($supplies_off_arr, $child->getAttribute('name'));
                }
            }
        }
        $al['power-off'] = $supplies_off_arr;
        
        array_push($alarms, $al);
    }

    return $alarms;
}

function getWeckerfromID($ID){
    $alarms = getWecker();
    foreach($alarms as $al){
      if($al['id'] == $ID)
          return $al;
    }
    return array();
}

function Get($val) {
    if (isset($_GET[$val]))
        return $_GET[$val];
    else
        return '';
}

?>
