<?php
/**
* Fetch the ohloh code history
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['project'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

$url = 'http://www.ohloh.net/p/'.$_GET['project'].'/analyses/latest/codehistory';

include_once(__DIR__.'/../Carica/StatusMonitor/Library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\Json(
    $url
  ),
  new Library\Filter\Xslt('xslt/ohloh/codehistory.xsl')
);

$feed->output();