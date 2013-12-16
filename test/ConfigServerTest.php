<?php
/**
 * @author      Daniel Kesberg <kesberg@ebene3.com>
 * @copyright   (c) 2013, ebene3 GmbH
 */

require '../src/Starbound/Config.php';
require '../src/Starbound/Config/Server.php';

class ConfigServerTest extends PHPUnit_Framework_TestCase {
    
    public function testReturnPasswordsAsArray()
    {
        $shouldReturn = array(
            'test',
            'debug',
            'password'
        );
        
        $logfile = './data/starbound.config';
        $config = new \Starbound\Config\Server($logfile);
        
        $this->assertEquals($shouldReturn, $config->getPasswords());
    }

    public function testAddAPassword()
    {
        $shouldReturn = array(
            'test',
            'debug',
            'password',
            'koala'
        );

        $logfile = './data/starbound.config';
        $config = new \Starbound\Config\Server($logfile);
        $config->addPassword('koala');

        $this->assertEquals($shouldReturn, $config->getPasswords());
    }

    public function testRemoveAPassword()
    {
        $shouldReturn = array(
            'test',
            'debug'
        );

        $logfile = './data/starbound.config';
        $config = new \Starbound\Config\Server($logfile);
        $config->removePassword('password');

        $this->assertEquals($shouldReturn, $config->getPasswords());
    }
}
