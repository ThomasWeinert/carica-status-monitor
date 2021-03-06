<?php
/**
* Fetch the xml of an Jenkins jobs listview and
* transform it using an xslt template.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

if (empty($_GET['view'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}

use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../Carica/StatusMonitor/Library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\Url($_GET['view']),
  new Library\Filter\Xslt(__DIR__.'/xslt/jenkins.xsl')
);

$feed->output();