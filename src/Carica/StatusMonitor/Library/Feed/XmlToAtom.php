<?php
/**
* Feed reading an XML data source and converting it to atom using XSLT
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Feed {

  use Carica\StatusMonitor\Library as Library;

  /**
   * Feed reading an XML data source and converting it to atom using XSLT
   */
  class XmlToAtom extends Library\Feed {

    /**
     * @var string
     */
    private $_url = '';
    /**
     * @var float|integer
     */
    private $_timeout = 3;
    /**
     * @var string
     */
    private $_xslt = '';

    /**
     * @var Carica\StatusMonitor\Library\Source\Url
     */
    private $_source = NULL;
    /**
     * @var Carica\StatusMonitor\Library\Filter\Xslt
     */
    private $_filter = NULL;

    /**
     * store url and optional xslt template
     *
     * @param string $url
     * @param string $xslt
     */
    public function __construct($url, $xslt = '') {
      $this->_url = $url;
      $this->_xslt = $xslt;
    }

    /**
     * Change the timeout for url requests
     * @param float $timeout
     */
    public function setTimeout($timeout) {
      $this->_timeout = (float)$timeout;
    }

    /**
     * Create a source for the provided url if needed
     *
     * @return \Carica\StatusMonitor\Library\Source\Url
     */
    protected function createSource() {
      return new Library\Source\Url(
        $this->_url, $this->_timeout
      );
    }

    /**
     * Create an xslt filter if an xslt file was provided
     *
     * @return \Carica\StatusMonitor\Library\Filter\Xslt|boolean
     */
    protected function createFilter() {
      if (!empty($this->_xslt)) {
        return new Library\Filter\Xslt($this->_xslt);
      }
      return FALSE;
    }
  }
}