<?php
/**
* Fetch the xhtml of an KVB station (Cologne Trams) and
* transform it using an xslt template.
*
* code = Station Code
*   Köln Hansaring: 008003392
*   Köln Hbf: 008000207
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['code'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

$parameters = array(
  'ld'=> 96236,
  'rt' => 1,
  'use_real_time_filter' => 0,
  'boardType' => 'Abfahrt',
  'input' => $_GET['code'],
  'REQTrain_name' => '',
  'productsFilter' => '1111111111000000',
  'maxJourneys' => 10,
  'date' => date('d.m.y'),
  'time' => date('Hi'),
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