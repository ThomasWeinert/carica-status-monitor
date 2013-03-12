<?php
/**
* Fetch graphite json data an transform it to atom/csm
*
* the url parameter should contain the whole url of the graphite resource
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
  new Library\Source\Json($_GET['url']),
  new Library\Filter\Xslt(__DIR__.'/xslt/graphite.xsl')
);

$feed->output();