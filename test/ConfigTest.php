<?php
/**
 * @author      Daniel Kesberg <kesberg@ebene3.com>
 * @copyright   (c) 2013, ebene3 GmbH
 */

require '../src/Starbound/Config.php';
require '../src/Starbound/Config/Server.php';
require '../src/Starbound/Config/Auth.php';

class ConfigTest extends PHPUnit_Framework_TestCase {
    
    public function testCreateConfig()
    {
        $logfile = './data/starbound.config';
        $config = new \Starbound\Config\Server($logfile);
        
        var_dump($config->getPasswords());
    }
}
