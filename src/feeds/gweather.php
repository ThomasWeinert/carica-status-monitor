<?php
/**
* Fetch the google weather xml and transform it to atom using an xslt template.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['location'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

$url = 'http://www.google.com/ig/api?weather=';
$url .= urlencode($_GET['location']);
$url .= empty($_GET['language']) ? '' : '&hl='.urlencode($_GET['language']);
$url .= '&ie=utf-8&oe=utf-8';

$feed = new Library\Feed(
  new Library\Source\Url($url),
  new Library\Filter\Xslt(__DIR__.'/weather/gweather.xsl')
);

error_reporting(E_ALL);
header('Content-Type: text/xml');
echo $feed->get();