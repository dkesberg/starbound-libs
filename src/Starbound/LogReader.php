<?php
/**
 * LogReader - A log reader for starbound server logs
 * 
 * Based on 
 * 
 * @author      Daniel Kesberg <dkesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

namespace Starbound;


class LogReader {

    const SERVER_OFFLINE    = 0;
    const SERVER_ONLINE     = 1;
    
    private $config;
    
    public $clients = array();

    public $server = array();

    public $parsetime = null;
    
    //public $timestamp = null;


    /**
     * @param $config
     */
    public function __construct(array $userConfig = array())
    {
        $this->config = array_merge(static::getDefaultConfig(), $userConfig);
        
        $this->checkServerStatus();
        
        if (isset($this->server['status']) && $this->server['status'] == self::SERVER_ONLINE) {
            $this->parseServerLog();    
        }
        
        //$this->timestamp = time();
    }
    
    public static function getDefaultConfig()
    {
        $config = array(
            'log.filename'  => 'starbound_server.log',
            'log.path'      => '/opt/Steam/SteamApps/common/Starbound/linux64/',
            'server.host'   => '127.0.0.1',
            'server.port'   => 21025,            
        );
        return $config;
    }
    
    public function getConfig()
    {
        return $this->config;
    }    
    
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
        
        $fp = @fopen($filepath, "r");
        
        if ($fp === false) {
            throw new \Exception('Can not read log file.');
        }
        
        while (($line = fgets($fp)) !== false) {
            
            if (strpos($line, '<User:')) {
                
                if (preg_match('/<User: ([0-9a-zA-Z-\s]+)>\s*([a-z]+).*$/i', $line, $matches) == 1) {
                    
                    if (isset($matches[1]) && !empty($matches[1])) {
                        $this->clients[$matches[1]] = trim($matches[2]);
                    }
                }
                
            } elseif (strpos($line, 'Server version')) {
                
                if (preg_match("/Info: Server version '([0-9a-zA-Z\.\s]*)' '([0-9]*)' '([0-9]*)'/i", $line, $matches) == 1) {
                    //$this->server['version'] = implode(' ', $matches);
                    $this->server['version'] = $matches[1];
                }
            }
        }
        fclose($fp);
        
        // filter disconnected clients
        if (!empty($this->clients)) {
            $this->clients = array_filter($this->clients, function($status) {
                return $status == 'connected';
            });
        }
        
        $this->parsetime = microtime() - $parsetime;
    }
    
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
    
}
