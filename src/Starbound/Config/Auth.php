<?php
/**
 * @author      Daniel Kesberg <kesberg@ebene3.com>
 * @copyright   (c) 2013, ebene3 GmbH
 */

namespace Starbound\Config;

use Starbound\Config as Config;

class Auth extends Config {

    public function getAccounts()
    {
        return $this->config->accounts;
    }
    
    
    public function addAccount($name, $password) {
        
        if (empty($name)) {
            throw new \InvalidArgumentException('$name can not be empty');
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('$password can not be empty');
        }
        
        $account = new \stdClass();
        $account->password = $password;
        
        $this->config->accounts->$name = $account;
    }
    
    public function removeAccount($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('$name can not be empty');
        }
        
        if (isset($this->config->accounts->$name)) {
            unset($this->config->accounts->$name);
        }
    }
}
