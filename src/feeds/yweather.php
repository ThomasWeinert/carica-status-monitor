<?php
/**
* Fetch the yahoo weather xml and transform it to atom using an xslt template.
* The original yweather elements will still be in the feed.
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

$url = 'http://weather.yahooapis.com/forecastrss?w=';
$url .= urlencode($_GET['location']);
$url .= '&u='.((isset($_GET['unit']) && $_GET['unit'] == 'f') ? 'f' : 'c');

$feed = new Library\Feed(
  new Library\Source\Url($url),
  new Library\Filter\Xslt(__DIR__.'/xslt/weather/yahoo.xsl')
);

error_reporting(E_ALL);
header('Content-Type: text/xml');
echo $feed->get();