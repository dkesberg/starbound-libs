<?php
/**
 * LogReader - A log reader for starbound server logs
 *
 * @author      Daniel Kesberg <dkesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

namespace Starbound;

class LogReader {

    /**
     * @var array
     */
    private $config;

    /**
     * Client array
     * Clients are stored as arrays with the following keys: name, ip, status
     * @var array
     */
    private $clients = array();

    /**
     * Server information as array.
     * Contains following keys: status, version, ip, hostname
     * 
     * @var array
     */
    private $server = array(
        'status'    => false,
        'version'   => 'unknown',
        'ip'        => null,
        'hostname'  => null
    );

    /**
     * Chatlog
     * Loglines are stored as arrays with the following keys: name, text
     * @var array
     */
    private $chatlog = array();

    /**
     * Parsetime in seconds
     * 
     * @var int|null
     */
    public $parsetime = null;

    /**
     * @param array $userConfig 
     */
    public function __construct(array $userConfig = array())
    {
        $this->config = array_merge(static::getDefaultConfig(), $userConfig);

        // populate server array with config values for offline display
        if (filter_var($this->config['server.host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->server['ip'] = $this->config['server.host'];
        } else {
            $this->server['hostname'] = $this->config['server.host'];
        }

        $this->checkServerStatus();

        if (isset($this->server['status']) && $this->server['status']) {
            $this->fetchServerInfo();
            $this->parseServerLog();
        }
    }

    /**
     * @return array the default config
     */
    public static function getDefaultConfig()
    {
        $config = array(
            'log.filename'  => 'starbound_server.log',
            'log.path'      => '/opt/Steam/SteamApps/common/Starbound/linux64',
            'log.fields'    => 'clients,version,chat,',
            'server.host'   => '127.0.0.1',
            'server.port'   => 21025,
        );
        return $config;
    }

    /**
     * @return array the current config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * parses the server log
     * 
     * @throws \Exception
     */
    private function parseServerLog()
    {
        $parsetime = microtime();
        
        $filepath = rtrim($this->config['log.path'], '/') . '/' . $this->config['log.filename'];
                
        if (!isset($filepath)) {
            throw new \Exception('Log file not set.');
        }
        
        if (!file_exists($filepath)) {
            throw new \Exception('Log file not found.');
        }
        
        $fp = fopen($filepath, "r");

        if ($fp === false) {
            throw new \Exception('Can not read log file.');
        }

        $clients = array();
        $fields = array_filter(array_map('trim', explode(',',$this->config['log.fields'])));

        while (($line = fgets($fp)) !== false) {
            // using strpos for faster parsing, so we dont need to preg_match every line
            if (in_array('clients', $fields)
                && strpos($line, 'Info: Client') !== false) {
                if (preg_match("/\\'(.*)\\'.*?\((.*?)\)(.*)/i", $line, $matches) == 1) {
                    if (isset($matches[1]) && !empty($matches[1])) {
                        $client = array(
                            'name'      => htmlentities(trim($matches[1])),
                            'ip'        => $matches[2],
                            'status'    => trim($matches[3])
                        );

                        $clients[$client['name']] = $client;
                    }
                }
                
            } elseif (in_array('version', $fields)
                && strpos($line, 'Server version') !== false) {
                if (preg_match("/Info: Server version '([0-9a-zA-Z\.\s]*)' '([0-9]*)' '([0-9]*)'/i", $line, $matches) == 1) {
                    //$this->server['version'] = implode(' ', $matches);
                    $this->server['version'] = $matches[1];
                }

            } elseif (in_array('chat', $fields)
                && strpos($line, 'Info:') !== false) {
                if (preg_match('/Info\:\s*<([^>]{2,})>\s?(.*)/i', $line, $matches)) {
                    if (isset($matches[1]) && !empty($matches[1])) {
                        $chatline = array(
                            'name'  => htmlentities(trim($matches[1])),
                            'text'  => htmlentities(trim($matches[2]))
                        );

                        $this->chatlog[] = $chatline;
                    }
                }
            }
        }

        // filter disconnected clients
        if (!empty($clients)) {
            $clients = array_filter($clients, function($client) {
                return $client['status'] == 'connected';
            });
        }

        // sort by player names
        if (!empty($clients)) {
            uksort($clients, 'strnatcasecmp');
        }
        $this->clients = $clients;

        fclose($fp);
        
        $this->parsetime = microtime() - $parsetime;
    }

    /**
     * checks and sets server status
     */
    private function checkServerStatus()
    {
        $fp = @fsockopen($this->config['server.host'], $this->config['server.port']);

        if ($fp) {
            $this->server['status'] = true;
            fclose($fp);
            
        } else {            
            $this->server['status'] = false;
        }
    }

    /**
     * fetches ip, hostname of the server
     */
    private function fetchServerInfo()
    {
        if ($this->server['ip'] !== null) {
            $hostname = gethostbyaddr($this->server['ip']);
            if ($hostname !== $this->server['ip']) {
                $this->server['hostname'] = $hostname;
            }

        } elseif ($this->server['hostname'] !== null) {
            $ip = gethostbyname($this->server['hostname']);
            if ($ip !== $this->server['hostname']) {
                $this->server['ip'] = $ip;
            }
        }
    }
    
    /**
     * gets the server status
     * 
     * @return bool
     */
    public function getServerStatus()
    {
        return $this->server['status'];
    }

    /**
     * get server info
     *
     * @return array
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * gets the player count
     * 
     * @return int
     */
    public function getPlayerCount()
    {
        return count($this->clients);
    }

    /**
     * get player list
     *
     * @return array
     */
    public function getPlayers()
    {
        return $this->clients;
    }

    /**
     * get chat log
     *
     * @param bool $desc set to true to reverse the sort order (newest messages on top)
     * @return array
     */
    public function getChatlog($desc = false)
    {
        if ($desc == true) {
            return array_reverse($this->chatlog);
        }
        return $this->chatlog;
    }

    /**
     * get json
     *
     * @return string
     */
    public function json()
    {
        $json = array(
            'server'        => $this->server,
            'playerlist'    => $this->clients,
            'playercount'   => $this->getPlayerCount(),
            'chatlog'       => $this->chatlog
        );

        return json_encode($json);
    }
}
