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

```


### Check server status

```
// returns true = online; false = offline
$serverStatus = $info->getServerStatus();
```

### Get server info
  
```
$serverInfo = $info->getServer();

// $serverInfo is an array like this
$serverInfo = array(
   'status'    => true,
   'version'   => 'Beta v. Offended Koala',
   'ip'        => '123.456.789.900',
   'hostname'  => 'starbound-server.name'
);
```

### Get amount of players

```
$activePlayers = $info->getPlayerCount();
```

### Get players

```
$players = $info->getPlayers();

// $players is an array like this
$players = array(
    'player1' => array(
        'name'      => 'player1',
        'ip'        => '123.145.678.900',
        'status'    => 'connected'
    ),
    ...
)
```

### Get chatlog

```
// default order: newest messages on bottom
$chatlog = $info->getChatlog();

// descending order: newest messages on top
$chatlog = $info->getChatlog(true);

// $chatlog is an array like this
$chatlog = array(
    array(
        'name'  => 'player1',
        'text'  => 'chatmessage'
    ),
    ...
)
```

### Get server & player info as json

```
$json = $info->json();
```

Examples
--------

Example for a webinterface: examples/basic.php

Example for json response: examples/json.php

Thanks to
---------

Jeremy Villemain, this class is based on his logs class  
[https://gist.github.com/lagonnebula/7928214](https://gist.github.com/lagonnebula/7928214)

[Starbound forum community](http://community.playstarbound.com/)
