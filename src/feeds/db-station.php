<?php
/**
* Fetch the xhtml of an KVB station (Cologne Trams) and
* transform it using an xslt template.
*
* code = Station Code
*   Köln Hansaring: 008003392
*   Köln Hbf: 008000207
*   Köln Süd: 238003361
*
* products = Trains Types to Display
*   11111: Only "real" trains
*   1111111111: All types of trains and buses
*
* offset = A time period
*   5 minutes: PT5M
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['code'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

$time = new DateTime();
$time->add(
  new DateInterval(empty($_GET['offset']) ? 'PT5M' : $_GET['offset'])
);


$parameters = array(
  'ld'=> 96236,
  'rt' => 1,
  'use_real_time_filter' => 0,
  'boardType' => 'Abfahrt',
  'input' => $_GET['code'],
  'REQTrain_name' => '',
  'productsFilter' => isset($_GET['products']) ? $_GET['products'] : '1111111111000000',
  'maxJourneys' => 10,
  'date' => $time->format('d.m.y'),
  'time' => $time->format('Hi'),
  'start' => 1,
  'ao' => 'yes'
);

$url = 'http://mobile.bahn.de/bin/mobil/bhftafel.exe/dox?';
foreach ($parameters as $name => $value) {
  $url .= '&'.$name.'='.urlencode($value);
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\HtmlPage($url),
  new Library\Filter\Xslt(__DIR__.'/xslt/traffic/db-station.xsl')
);

$feed->output();
