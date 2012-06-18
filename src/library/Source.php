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