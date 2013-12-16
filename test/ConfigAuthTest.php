<?php
/**
 * @author      Daniel Kesberg <kesberg@ebene3.com>
 * @copyright   (c) 2013, ebene3 GmbH
 */

require '../src/Starbound/Config.php';
require '../src/Starbound/Config/Auth.php';

class ConfigAuthTest extends PHPUnit_Framework_TestCase {
    
    public function testReadsConfigFile()
    {        
        $logfile = './data/accesscontrol.config';
        $config = new \Starbound\Config\Auth($logfile);
        
        $this->assertTrue(is_a($config->getAccounts(), 'stdClass'));
    }   
    
    public function testAddAccount()
    {
        $user       = 'php';
        $password   = 'unit';
        
        $logfile = './data/accesscontrol.config';
        $config = new \Starbound\Config\Auth($logfile);
        
        $config->addAccount($user, $password);
        
        $accounts = $config->getAccounts();
        $this->assertObjectHasAttribute('php', $accounts);
        $this->assertObjectHasAttribute('password', $accounts->php);
        $this->assertTrue($accounts->php->password == 'unit');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $name can not be empty
     */
    public function testAddAccountThrowsExceptionOnMissingName()
    {
        $logfile = './data/accesscontrol.config';
        $config = new \Starbound\Config\Auth($logfile);

        $config->addAccount('','phpunit');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $password can not be empty
     */
    public function testAddAccountThrowsExceptionOnMissingPassword()
    {
        $logfile = './data/accesscontrol.config';
        $config = new \Starbound\Config\Auth($logfile);

        $config->addAccount('phpunit','');
    }

    public function testRemoveAccount()
    {
        $user       = 'php';
        $password   = 'unit';

        $logfile = './data/accesscontrol.config';
        $config = new \Starbound\Config\Auth($logfile);

        $config->addAccount($user, $password);
        $config->removeAccount($user);
        $accounts = $config->getAccounts();

        $this->assertFalse(isset($accounts->php));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $name can not be empty
     */
    public function testRemoveAccountThrowsExceptionOnMissingName()
    {
        $logfile = './data/accesscontrol.config';
        $config = new \Starbound\Config\Auth($logfile);
        
        $config->removeAccount('');
    }
}
