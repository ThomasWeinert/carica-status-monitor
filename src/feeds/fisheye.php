<?php
/**
* Fetch an rss feed an transform it to atom
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['url'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../Carica/StatusMonitor/Library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\Url($_GET['url'], empty($_GET['timeout']) ? 5 : (int)$_GET['timeout']),
  new Library\Filter\Xslt(__DIR__.'/xslt/fisheye/activities.xsl')
);

$feed->output();