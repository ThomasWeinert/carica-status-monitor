<?php
/**
* Fetch the xhtml of an KVB station (Cologne Trams) and
* transform it using an xslt template.
*
* code = Station Code
*   Köln Hansaring: 008003392
*   Köln Hbf: 238000207
*   Köln Süd: 238003361
*
* products = Trains Types to Display
*   tailing zeros can  be skipped
*   1 1 1 1 1 1 1 1
*   | | | | | | | Trams
*   | | | | | | | Metro Lines
*   | | | | | | Ferries
*   | | | | | Buses
*   | | | | Commuter (S) Trains
*   | | | Local Trains (RB + RE ...)
*   | | Express (IR + D) Trains
*   | InterCity (IC) Trains
*   InterCityExpress (ICE) Trains
*
* So if you like only "real" trains, it's 11111, if you like all types
*    of transport, it's 11111111, if you like only IC trains, it's 01
*
* offset = A time period
*   5 minutes: PT5M
*
* Optionally you can change the delay in minutes after a train is considered late or really late.
*
* delay_warning = 10 (default, 10 minutes)
* delay_error = 120 (default, 120 minutes)
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

$delayWarning = empty($_GET['delay_warning']) ? 10 : (int)$_GET['delay_warning'];
$delayError = empty($_GET['delay_error']) ? 120 : (int)$_GET['delay_error'];

$url = 'http://mobile.bahn.de/bin/mobil/bhftafel.exe/dox?';
foreach ($parameters as $name => $value) {
  $url .= '&'.$name.'='.urlencode($value);
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../Carica/StatusMonitor/Library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\HtmlPage($url),
  new Library\Filter\Xslt(
    __DIR__.'/xslt/traffic/db-station.xsl',
    array(
      'DELAY_MINUTES_WARNING' => $delayWarning,
      'DELAY_MINUTES_ERROR' => $delayError
    )
  )
);

$feed->output();
