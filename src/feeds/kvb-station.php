<?php
/**
* Fetch the xhtml of an KVB station (Cologne Trams) and
* transform it using an xslt template.
*
* code = Station Code
*   Hansaring: 36
*   Barbarossaplatz: 23
*   Zülpicher Platz: 24
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['code'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

$url = 'http://www.kvb-koeln.de/generated/?aktion=show&title=none&code='.(int)$_GET['code'];

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../Carica/StatusMonitor/Library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\HtmlPage($url),
  new Library\Filter\Xslt(__DIR__.'/xslt/traffic/kvb-station.xsl')
);

$feed->output();