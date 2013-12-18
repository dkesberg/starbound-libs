<?php
/**
 * @author      Daniel Kesberg <kesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

namespace Starbound;


class Config {

    protected $filename;
    
    protected $config = array();
    
    public function __construct($filename)
    {
        $this->filename = $filename;
        
        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException('$filename is not a valid file path.');    
        }
        
        $this->readConfig();
    }
    
    protected function readConfig() {
        $json = file_get_contents($this->filename);
        if ($json === false) {
            throw new \Exception('Reading logfile failed. Can not read file.');
        }
        $this->config = json_decode($json);
    }
    
    protected function writeConfig() {
        if (file_put_contents($this->filename, json_encode($this->config)) === false) {
            throw new \Exception('Writing logfile failed. Can not write file.');
        }
    }
    
    public function getConfig($json = false) {
        if ($json) {
            return json_encode($this->config);            
        }
        
        return $this->config;
    }
    
    public function save() {
        $this->writeConfig();
    }
    
}
