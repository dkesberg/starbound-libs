<?php
/**
 * @author      Daniel Kesberg <kesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

require '../src/Starbound/Config.php';
require '../src/Starbound/Config/Server.php';
require '../src/Starbound/Config/Auth.php';

class ConfigTest extends PHPUnit_Framework_TestCase {
    
    public function testCreateConfigAndReadLogfile()
    {
        $logfile = './data/ConfigTest/starbound.config';
        $config = new \Starbound\Config($logfile);

        $this->assertEquals($config->getConfig(), json_decode(file_get_contents($logfile)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $filename is not a valid file path.
     */
    public function testThrowExceptionOnInvalidFilepath()
    {
        $logfile = 'starbound.config';
        $config = new \Starbound\Config($logfile);
    }

    /**
     * i'm not sure how to test but write error should throw an exception, so: no exception = everything is fine ?
     */
    public function testWriteLogfile()
    {
        $logfile = './data/ConfigTest/starbound.config';
        $config = new \Starbound\Config($logfile);
        
        $config->save();
    }
}
