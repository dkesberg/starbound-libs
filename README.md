starbound-libs
==============

PHP class to grab some information from a starbound server. Hopefully more to come ;)


How to use
----------

```
// include class
require_once '../src/Starbound/LogReader.php';

// build a reader object with your logfile path
$info = new LogReader(array(
    'log.path' => '/path/to/your/starbound/directory/Steam/SteamApps/common/Starbound/linux64'
));

// check if server is online
// returns true = online; false = offline
$serverStatus = $info->getServerStatus();

// get amount of players
$activePlayers = $info->getPlayerCount();

// get server info as array
// contains following keys: status, version, ip, hostname
$serverInfo = $info->server;

// get connected players as an sorted array of playernames
$players = $info->clients;

// get parsetime (only for parsing the logfile)
$parsetime = $info->parsetime;

```

You can also find an example in public/example.php
