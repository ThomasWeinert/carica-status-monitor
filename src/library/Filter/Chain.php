<?php
/**
* Transform a DOM using the provided filters.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Filter {

  use Carica\StatusMonitor\Library as Library;

  /**
* Transform a DOM using the provided filters.
   */
  class Chain implements \Carica\StatusMonitor\Library\Filter {

    /**
     * @var array(\Carica\StatusMonitor\Library\Filter)
     */
    private $_filters = array();

    /**
     */
    public function __construct() {
      foreach (func_get_args() as $filter) {
        $this->attach($filter);
      }
    }

    /**
     * Attach a new filter to the chain
     * @param \Carica\StatusMonitor\Library\Filter $filter
     */
    public function attach(Library\Filter $filter) {
      $this->_filters[] = $filter;
    }

    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom) {
      foreach ($this->_filters as $filter) {
        $dom = $filter->filter($dom);
      }
      return $dom;
    }
  }
}