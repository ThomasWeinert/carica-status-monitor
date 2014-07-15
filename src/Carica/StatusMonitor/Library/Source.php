<?php
/**
* A source creates an DOMDocument.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library {

  /**
   * A source creates an DOMDocument
   */
  interface Source {

    /**
     * @return \DOMDocument|NULL
     */
    public function read();

  }
}

namespace Carica\StatusMonitor\Library\Source {

  /**
   * Validate that it start with http:// oder
   * https://, map http://example.tld/ to the local example files
   * directory.
   *
   * @throws UnexpectedValueException
   * @return string
   */
  function validateUrl($url) {
    if (!preg_match('(^https?://)', $url)) {
      throw new \UnexpectedValueException('Source url is invalid.');
    }
    $exampleUrl = 'http://example.tld/';
    if (0 === strpos($url, $exampleUrl)) {
      $result = realpath(
        __DIR__.'/../../../../tests/files/'.substr($url, strlen($exampleUrl))
      );
      if (!empty($result)) {
        return $result;
      }
    }
    return $url;
  }
}