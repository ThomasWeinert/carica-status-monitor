<?php
/**
* A proxy for atom feeds, the brower (js) can only load xml from the same
* domain so an proxy script is needed to load atom feed from ohter servers.
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

$feed = new Library\Feed(
  new Library\Source\Url(
    $_GET['url']'
  )
);

$feed->output();