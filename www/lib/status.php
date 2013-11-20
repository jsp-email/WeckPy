<?php

/* Status functions */

function getUptime() {

    $file = file_get_contents('/proc/uptime');
    $time = preg_split('#\s+#', trim($file));

    return round($time[0], 0);
}

function getSysLoad() {

    return sys_getloadavg();
}

function getMemoryUsage() {

    exec('free -bo', $data);
    list($type, $total, $used, $free, $shared, $buffers, $cached) = preg_split('#\s+#', $data[1]);
    $usage = round(($used - $buffers - $cached) / $total * 100);

    return array('percent' => $usage, 'total' => (int) $total, 'free' => ($free + $buffers + $cached), 'used' => ($used - $buffers - $cached));
}

function getSwapUsage() {

    exec('free -bo', $data);
    list($type, $total, $used, $free) = preg_split('#\s+#', $data[2]);
    $usage = 0;
    if ($total > 0 || $used > 0) {

        $usage = round($used / $total * 100);
    }

    return array('percent' => $usage, 'total' => (int) $total, 'free' => (int) $free, 'used' => (int) $used);
}

function getCoreTemprature() {

    $file = @file_get_contents('/sys/class/thermal/thermal_zone0/temp');
    if ($file != false) {

        return round(substr(trim($file), 0, 2) . '.' . substr(trim($file), 2), 2);
    } else {

        return 0.0;
    }
}

function getCpuClock() {

    if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) {

        $file = trim(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq'));
        return floatval(substr($file, 0, -3) . '.' . substr($file, -3));
    } else {

        $file = file_get_contents('/proc/cpuinfo');
        preg_match('#BogoMIPS\s*:\s*(\d*\.\d*)#i', $file, $match);
        return floatval($match[1]);
    }
}

function getMemoryInfo() {
    exec('df -lT | grep -vE "tmpfs|rootfs|Filesystem|Dateisystem"', $data);

    $devices = array();
    $totalSize = 0;
    $usedSize = 0;
    foreach ($data as $row) {

        list($device, $type, $blocks, $use, $available, $used, $mountpoint) = preg_split('#[^\dA-Z/]+#i', $row);

        $totalSize += $blocks * 1024;
        $usedSize += $use * 1024;

        $devices[] = array(
            'device' => $device,
            'type' => $type,
            'total' => $blocks * 1024,
            'used' => $use * 1024,
            'free' => $available * 1024,
            'percent' => round(($use * 100 / $blocks), 0),
            'mountpoint' => $mountpoint
        );
    }

    $devices[] = array('total' => $totalSize, 'used' => $usedSize, 'free' => $totalSize - $usedSize, 'percent' => round(($usedSize * 100 / $totalSize), 0));
    return $devices;
}

function getNetworkDevices() {

    $dev = file_get_contents('/proc/net/dev');
    $devices = preg_split('#\n#', $dev, -1, PREG_SPLIT_NO_EMPTY);
    unset($devices[0], $devices[1]);

    $netDev = array();
    foreach ($devices as $device) {

        list($dev_name, $stats) = preg_split('#:#', $device);
        $stats = preg_split('#\s+#', trim($stats));
        $netDev[] = array(
            'name' => trim($dev_name),
            'in' => $stats[0],
            'out' => $stats[8],
            'errors' => $stats[2] + $stats[10],
            'drops' => $stats[3] + $stats[11]
        );
    }

    return $netDev;
}

function getUsbDevices() {

    exec('lsusb', $data);
    $devices = array();

    foreach ($data as $row) {

        preg_match('#[0-9a-f]{4}:[0-9a-f]{4}\s+(.+)#i', $row, $match);
        $devices[] = trim($match[1]);
    }

    return $devices;
}

/* Helper Functions */

function formatBytesBinary($size, $short = true) {

    if ($short === true) {
        $norm = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB',
            'EiB', 'ZiB', 'YiB');
    } else {
        $norm = array('Byte',
            'Kibibyte',
            'Mebibyte',
            'Gibibyte',
            'Tebibyte',
            'Pebibyte',
            'Exbibyte',
            'Zebibyte',
            'Yobibyte');
    }

    $factor = 1024;

    $count = count($norm) - 1;

    $x = 0;
    while ($size >= $factor && $x < $count) {
        $size /= $factor;
        $x++;
    }

    $size = number_format($size, 2, ',', '.') . ' ' . $norm[$x];
    return $size;
}

function formatTimefromSeconds($seconds, $short = false) {

    if ($seconds < 0) {
        $seconds = $seconds * -1;
    }

    $jears = 0;
    $month = 0;
    $weeks = 0;
    $days = 0;
    $hours = 0;
    $minutes = 0;
    $sec = 0;
    $jears_in_sec = 365 * 24 * 60 * 60;
    if ($seconds >= $jears_in_sec) {
        $jears = floor($seconds / $jears_in_sec);
        $seconds -= $jears * $jears_in_sec;
    }
    $month_in_sec = 30 * 24 * 60 * 60;
    if ($seconds >= $month_in_sec) {
        $month = floor($seconds / $month_in_sec);
        $seconds -= $month * $month_in_sec;
    }
    $weeks_in_sec = 7 * 24 * 60 * 60;
    if ($seconds >= $weeks_in_sec) {
        $weeks = floor($seconds / $weeks_in_sec);
        $seconds -= $weeks * $weeks_in_sec;
    }
    $days_in_sec = 24 * 60 * 60;
    if ($seconds >= $days_in_sec) {
        $days = floor($seconds / $days_in_sec);
        $seconds -= $days * $days_in_sec;
    }
    if ($seconds >= 3600) {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
    }
    if ($seconds >= 60) {
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
    }
    $sec = $seconds;

    $first = false;
    $string = '';
    if ($jears > 0 || $first == true) {
        if ($short == true) {
            $string .= $jears . ' J, ';
        } else {
            $string .= $jears . ' ' . ($jears == 1 ? 'Jahr' : 'Jahre') . ', ';
        }
        $first = true;
    }
    if ($month > 0 || $first == true) {
        if ($short == true) {
            $string .= $month . ' M, ';
        } else {
            $string .= $month . ' ' . ($month == 1 ? 'Monat' : 'Monate') . ', ';
        }
        $first = true;
    }
    if ($weeks > 0 || $first == true) {
        if ($short == true) {
            $string .= $weeks . ' W, ';
        } else {
            $string .= $weeks . ' ' . ($weeks == 1 ? 'Woche' : 'Wochen') . ', ';
        }
        $first = true;
    }
    if ($days > 0 || $first == true) {
        if ($short == true) {
            $string .= $days . ' D, ';
        } else {
            $string .= $days . ' ' . ($days == 1 ? 'Tag' : 'Tage') . ', ';
        }
        $first = true;
    }
    if ($hours > 0 || $first == true) {
        if ($short == true) {
            $string .= $hours . ' S, ';
        } else {
            $string .= $hours . ' ' . ($hours == 1 ? 'Stunde' : 'Stunden') . ', ';
        }
        $first = true;
    }
    if ($minutes > 0 || $first == true) {
        if ($short == true) {
            $string .= $minutes . ' min, ';
        } else {
            $string .= $minutes . ' ' . ($minutes == 1 ? 'Minute' : 'Minuten') . ', ';
        }
        $first = true;
    }
    if ($short == true) {
        $string .= $sec . ' s';
    } else {
        $string .= $sec . ' ' . ($sec == 1 ? 'Sekunde' : 'Sekunden');
    }

    return trim($string);
}

?>