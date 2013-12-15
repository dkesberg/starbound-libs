<?php
/**
 * @author      Daniel Kesberg <kesberg@gmail.com>
 * @copyright   (c) 2013, Daniel Kesberg
 */

error_reporting(E_ALL);
ini_set('display_errors', false);

require_once '../src/Starbound/LogReader.php';
use Starbound\LogReader as LogReader;

$logreader = new LogReader(array(
    'log.path' => '/home/starbound-server/Steam/SteamApps/common/Starbound/linux64'
));

header('Content-type: application/json');
echo $logreader->json();