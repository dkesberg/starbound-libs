<?php
/**
 * @author      Daniel Kesberg <kesberg@ebene3.com>
 * @copyright   (c) 2013, ebene3 GmbH
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
    
    private function readConfig() {
        $this->config = json_decode(file_get_contents($this->filename));         
    }
    
    private function writeConfig() {
        if (file_put_contents($this->filename, json_encode($this->config)) === false) {
            throw new \Exception('Writing logfile failed. Can not write file.');
        }
    }
    
    public function save() {
        $this->writeConfig();
    }
    
}
