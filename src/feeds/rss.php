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

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed\XmlToAtom(
  $_GET['url'],
  __DIR__.'/xslt/rss/atom.xsl'
);
if (!empty($_GET['timeout'])) {
  $feed->setTimeout($_GET['timeout']);
}

$feed->output();