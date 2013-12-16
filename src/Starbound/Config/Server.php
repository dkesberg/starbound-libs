<?php
/**
 * @author      Daniel Kesberg <kesberg@ebene3.com>
 * @copyright   (c) 2013, ebene3 GmbH
 */

namespace Starbound\Config;

use Starbound\Config as Config;

class Server extends Config {
    
    public function addPassword($password)
    {
        $this->config->serverPasswords = array_merge($this->config->serverPasswords, array($password));
    }
    
    public function removePassword($password)
    {
        $this->config->serverPasswords = array_diff($this->config->serverPasswords, array($password));
    }
    
    public function getPasswords()
    {
        return $this->config->serverPasswords;
    }
}
