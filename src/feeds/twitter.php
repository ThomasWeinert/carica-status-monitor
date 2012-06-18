<?php
/**
* Do a Twitter search and return the result.
*
* Twitter provides a nice Atom feed.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['q'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\Url(
    'http://search.twitter.com/search.atom?q='.urlencode($_GET['q'])
  )
);

header('Content-Type: text/xml');
echo (string)$feed;