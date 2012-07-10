<?php
/**
* Transform a DOM using an xslt file.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Filter {

  /**
   * Transform a DOM using an xslt file.
   */
  class Xslt implements \Carica\StatusMonitor\Library\Filter {

    /**
     * @var \XSLTProcessor
     */
    private $_processor = NULL;

    /**
     * @var string
     */
    private $_xsltFile = '';

    public function __construct($xsltFile) {
      $this->_xsltFile = $xsltFile;
    }

    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom) {
      $template = new \DOMDocument();
      $template->load($this->_xsltFile);
      $this->processor()->importStylesheet($template);
      return $this->processor()->transformToDoc($dom);
    }

    /**
     * @param \XSLTProcessor $processor
     * @return \XSLTProcessor
     */
    public function processor(\XSLTProcessor $processor = NULL) {
      if (isset($processor)) {
        $this->_processor = $processor;
      } elseif (NULL == $this->_processor) {
        $this->_processor = new \XSLTProcessor();
      }
      return $this->_processor;
    }
  }
}