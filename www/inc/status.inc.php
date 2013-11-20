<?php
include_once('lib/status.php');

//Systemzeit
$uptime = formatTimefromSeconds(getUptime());
$data['uptimeSort'] = $uptime;
$data['uptime'] = $uptime;

$date = new DateTime();
$date = $date->getTimestamp() - getUptime();
$data['lastBootTime'] = date('d.m.Y H:i:s', $date);

//CPU
$sysload = getSysLoad();
$data['sysload_0'] = $sysload[0];
$data['sysload_1'] = $sysload[1];
$data['sysload_2'] = $sysload[2];

$data['cpuClock'] = number_format(getCpuClock(), 2, ',', '.');

$data['coreTemp'] = number_format(getCoreTemprature(), 2, ',', '.');

//Speicher
$memory = getMemoryUsage();
$data['memoryPercent'] = $memory['percent'];
$data['memoryPercentDisplay'] = number_format($memory['percent'], 0, ',', '.');
$data['memoryTotal'] = formatBytesBinary($memory['total']);
$data['memoryFree'] = formatBytesBinary($memory['free']);
$data['memoryUsed'] = formatBytesBinary($memory['used']);

//Swap
$swap = getSwapUsage();
$data['swapPercent'] = $swap['percent'];
$data['swapPercentDisplay'] = number_format($swap['percent'], 0, ',', '.');
$data['swapTotal'] = formatBytesBinary($swap['total']);
$data['swapFree'] = formatBytesBinary($swap['free']);
$data['swapUsed'] = formatBytesBinary($swap['used']);

//Systemspeicher
$sysMemory = getMemoryInfo();
$data['sysMemory'] = '';
foreach ($sysMemory as $index => $mem) {

    if ($index != (count($sysMemory) - 1)) {

        $data['sysMemory'] .= '
            <tr>
                <td>' . @htmlentities($mem['device'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . @htmlentities($mem['mountpoint'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . @htmlentities($mem['percent'], ENT_QUOTES, 'UTF-8') . '%</td>
                <td>' . formatBytesBinary($mem['total']) . '</td>
                <td>' . formatBytesBinary($mem['used']) . '</td>
                <td>' . formatBytesBinary($mem['free']) . '</td>
            </tr>';
    }
}

//Netzwerk
$network = getNetworkDevices();
$data['network'] = '';
foreach ($network as $index => $net) {

    $data['network'] .= '
            <tr>
                <td>' . @htmlentities($net['name'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . formatBytesBinary($net['in']) . '</td>
                <td>' . formatBytesBinary($net['out']) . '</td>
                <td>' . number_format($net['errors'], 0, ',', '.') . '/' . number_format($net['drops'], 0, ',', '.') . '</td>
            </tr>';
}
?>
<div class="panel panel-primary">
    <div class="panel-heading">Systemzeit</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                Laufzeit:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['uptime'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                letzter Start:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['lastBootTime'] ?>
            </div>
        </div>
    </div>
</div>    

<div class="panel panel-primary">
    <div class="panel-heading">System</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                CPU Auslastung:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['sysload_0'] ?>  > <?= $data['sysload_1'] ?> > <?= $data['sysload_2'] ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                CPU Takt:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['cpuClock'] ?> MHz
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                Temperatur:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['coreTemp'] ?>  &deg; C
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                RAM:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['memoryPercentDisplay'] ?> %    (<?= $data['memoryUsed'] ?> / <?= $data['memoryTotal'] ?> )
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-2" style="text-align: right">
                SWAP:
            </div>
            <div class="col-xs-6  col-sm-8 col-md-10">
                <?= $data['swapPercentDisplay'] ?>  %  (<?= $data['swapUsed'] ?> / <?= $data['swapTotal'] ?> )
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Storage</div>
    <div class="panel-body">

        <div class="row">
            <table class="table table-hover table-striped" id="no-more-tables">
                <thead>
                    <tr>
                        <th>Partition</th>
                        <th>Mountpoint</th>
                        <th>Auslastung</th>
                        <th>Gesamt</th>
                        <th>Belegt</th>
                        <th>Frei</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <?= $data['sysMemory'] ?>
            </table>

        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Network</div>
    <div class="panel-body">

        <div class="row">
            <table class="table table-hover table-striped" id="no-more-tables">
                <thead>
                    <tr>
                        <th>Schnittstelle</th>
                        <th>Empfangen</th>
                        <th>Gesendet</th>
                        <th>Fehler/Verloren</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <?= $data['network'] ?>
            </table>

        </div>
    </div>
</div>






