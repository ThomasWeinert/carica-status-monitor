<?php
/**
* Twitter provides nice Atom feeds.
*
* Usage:
*
* Do a Twitter search:.
*   twitter.php?q=%23papayaCMS
*
* Fetch the User timeline:.
*   twitter.php?user=papayaCMS
*
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['q']) && empty($_GET['user'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

if (!empty($_GET['user'])) {
  $url = 'http://twitter.com/statuses/user_timeline/'.urlencode($_GET['user']).'.atom';
} else {
  $url = 'http://search.twitter.com/search.atom?q='.urlencode($_GET['q']);
}

$feed = new Library\Feed(
  new Library\Source\Url(
    $url
  )
);

header('Content-Type: text/xml');
echo (string)$feed;