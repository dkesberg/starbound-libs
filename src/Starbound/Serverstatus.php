<?php
/**
 * @author      Daniel Kesberg <kesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

namespace Starbound;


class Serverstatus {
    
    protected $config = array(
        'host' => '127.0.0.1',
        'port' => 21025
    );

    /**
     * true = online; false = offline
     * @var bool
     */
    protected $status = false;

    /**
     * @param array $userConfig
     */
    public function __construct(array $userConfig = array())
    {
        $this->config = array_merge($this->config, $userConfig);

        $this->checkStatus();
    }

    /**
     * checks and sets server status
     */
    public function checkStatus()
    {
        $fp = @fsockopen($this->config['host'], $this->config['port']);

        if ($fp) {
            $this->status = true;
            fclose($fp);

        } else {
            $this->status = false;
        }
    }

    /**
     * gets the server status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * get config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
