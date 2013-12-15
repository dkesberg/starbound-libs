<?php
/**
 * LogReader - A log reader for starbound server logs
 * 
 * Based on the logs class by Jeremy Villemain
 * https://gist.github.com/lagonnebula/7928214
 * 
 * @author      Daniel Kesberg <dkesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

namespace Starbound;

class LogReader {

    /**
     * Constants for server status
     */
    const SERVER_OFFLINE    = 0;
    const SERVER_ONLINE     = 1;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $clients = array();

    /**
     * Server information as array.
     * Contains following keys: status, version, ip, hostname
     * 
     * @var array
     */
    public $server = array(
        'status'    => self::SERVER_OFFLINE,
        'version'   => 'unknown',
        'ip'        => null,
        'hostname'  => null
    );

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
        
        $this->checkServerStatus();
        
        $this->fetchServerInfo();
        
        if (isset($this->server['status']) && $this->server['status'] == self::SERVER_ONLINE) {
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
        while (($line = fgets($fp)) !== false) {
            // using strpos for faster parsing, so we dont need to preg_match every line
            if (strpos($line, 'Client')) {
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
                
            } elseif (strpos($line, 'Server version')) {
                if (preg_match("/Info: Server version '([0-9a-zA-Z\.\s]*)' '([0-9]*)' '([0-9]*)'/i", $line, $matches) == 1) {
                    //$this->server['version'] = implode(' ', $matches);
                    $this->server['version'] = $matches[1];
                }
            }
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
            $this->server['status'] = self::SERVER_ONLINE;
            fclose($fp);
            
        } else {            
            $this->server['status'] = self::SERVER_OFFLINE;
        }
    }

    /**
     * fetches ip, hostname of the server
     */
    private function fetchServerInfo()
    {
        $hostname = gethostname();
        if ($hostname !== false) {            
            $this->server['hostname'] = $hostname;
            
            $ip = gethostbyname($hostname);
            if ($ip !== $hostname) {
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
        return ($this->server['status'] == self::SERVER_ONLINE) ? true : false;
    }

    /**
     * gets the player count
     * 
     * @return int
     */
    public function getPlayerCount($offline = false)
    {
        if (!$offline) {
            return count($this->getPlayers(true));
        }

        return count($this->clients);
    }

    public function getPlayers($offline = false)
    {
        if (!$offline) {
            return $this->clients;
        }

        // filter disconnected clients
        $clients = array();
        if (!empty($this->clients)) {
            $clients = array_filter($this->clients, function($client) {
                return $client['status'] == 'connected';
            });
        }
        return $clients;
    }
}
