<?php

if (empty($_GET['q'])) {
  header('Status: 400 Bad Request', TRUE, 400);
  exit;
}
header('Content-Type: text/xml');
readfile(
  'http://search.twitter.com/search.atom?q='.urlencode($_GET['q'])
);