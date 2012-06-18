<?php

if (empty($_GET['view'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\Url($_GET['view']),
  new Library\Filter\Xslt(__DIR__.'/jenkins/view.xsl')
);

header('Content-Type: text/xml');
echo $feed;