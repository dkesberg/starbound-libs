<?php
/**
 * @author      Daniel Kesberg <kesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

require_once '../src/Starbound/LogReader.php';

use Starbound\LogReader as LogReader;

$logreader = new LogReader(array(
    'log.path' => '/home/starbound-server/Steam/SteamApps/common/Starbound/linux64'
));
?>
<!doctype html>
<html lang="en-US">
<head>    
    <meta charset="UTF-8">
    <title>Starbound Server Info</title>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <style type="text/css">
        body {
            padding-top: 70px;
        }
        table > tbody > tr > td.server-status {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Starbound Server Info</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-globe"></span> Server</div>
                <div class="panel-body">
                    <table class="table table-condensed table-bordered">
                        <tbody>
                        <tr>
                            <th>Status</th>
                            <td class="server-status">
                                <span class="label label-<?= ($logreader->getServerStatus() == 1) ? 'success' : 'danger' ; ?>">
                                <?= ($logreader->getServerStatus() == 1) ? 'Online' : 'Offline' ; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Version</th>
                            <td><?= $logreader->server['version']; ?></td>
                        </tr>
                        <tr>
                            <th>Hostname</th>
                            <td><?= $logreader->server['hostname']; ?></td>
                        </tr>
                        <tr>
                            <th>IP</th>
                            <td><?= $logreader->server['ip']; ?></td>
                        </tr>
                        <tr>
                            <th>Players Online</th>
                            <td><?= $logreader->getPlayerCount(); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Players</div>
                <div class="panel-body">
                    <?php if ($logreader->getPlayerCount()): ?>
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Playername</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($logreader->clients as $playerName): ?>
                                <tr>
                                    <td>
                                        <?= $playerName; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        No active players
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <span class="label label-default">
                Log parse time: <?= $logreader->parsetime; ?> seconds.
            </span>
        </div>
    </div>
</div>
</body>
</html>



