<?php
/**
* A filter transforms the DOMDocument.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library {

  /**
   * A filter transforms the DOMDocument.
   */
  interface Filter {

    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom);

  }
}