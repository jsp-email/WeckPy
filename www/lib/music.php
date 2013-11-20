<?php
/* Settings */
$mpdServer = 'localhost';
$mpdPort = '6600';
$mpdPassword = NULL;
$volDownSteps = 2;
$volUpSteps = 2;


/* Do not change */
ob_start();

include 'mpd.class.php';

$mpd = new mpd($mpdServer, $mpdPort, $mpdPassword);


switch($_GET['a']) {
		case 'volup':
			$mpd->AdjustVolume($volUpSteps); break;
		case 'voldown':
			$mpd->AdjustVolume('-'.$volDownSteps); break;
		case 'play':
			$mpd->Play(); break;
		case 'pause':
			$mpd->Pause(); break;
		case 'prev':
			$mpd->Previous(); break;
		case 'next':
			$mpd->Next(); break;
		case 'stop':
			$mpd->Stop(); break;
		case 'start':
			$songID = (int) $_GET['id']; 
			$mpd->SkipTo($songID); 
			break;
		case 'clearpl':
			$mpd->PLClear();  
			break;
		case 'remove':
			$songID = (int) $_GET['id']; 
			$mpd->SendCommand('deleteid', $songID); 
			$mpd->RefreshInfo();  
			break;
	}
  

  
?>