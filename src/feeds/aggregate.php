<?php
/**
* A proxy for atom feeds, the brower (js) can only load xml from the same
* domain so an proxy script is needed to load atom feed from ohter servers.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['rss']) && empty($_GET['atom'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../Carica/StatusMonitor/Library/Autoloader.php');
Library\Autoloader::register();

$feeds = array();
if (isset($_GET['rss']) && is_array($_GET['rss'])) {
  foreach ($_GET['rss'] as $url) {
    $feeds[] = new Library\Feed\XmlToAtom($url, __DIR__.'/xslt/rss/atom.xsl');
  }
}
if (isset($_GET['atom']) && is_array($_GET['atom'])) {
  foreach ($_GET['atom'] as $url) {
    $feeds[] = new Library\Feed\XmlToAtom($url);
  }
}

$feed = new Library\Feed(
  new Library\Source\Aggregate($feeds),
  new Library\Filter\Xslt('xslt/atom/order-by-updated.xsl')
);

$feed->output();