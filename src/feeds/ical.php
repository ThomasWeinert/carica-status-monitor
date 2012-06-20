<?php
/**
* Reading iCalendar files
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/
use Carica\StatusMonitor\Library as Library;

include_once(__DIR__.'/../library/Autoloader.php');
Library\Autoloader::register();

$feed = new Library\Feed(
  new Library\Source\Icalendar(
    'http://example.tld/hcking.de.ical'
  )
);

header('Content-Type: text/xml');
echo (string)$feed;