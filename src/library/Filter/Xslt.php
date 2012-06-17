<?php

namespace Carica\StatusMonitor\Library\Filter {

  class Xslt implements \Carica\StatusMonitor\Library\Filter {

    /**
     * @var \XSLTProcessor
     */
    private $_processor = NULL;

    public function __construct($xsltFile) {
      $this->_xsltFile = $xsltFile;
    }

    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom) {
      $template = new DOMDocument();
      $template->load($xsltFile);
      $this->processor->importStylesheet($template);
      return $this->processor()->transformToXml($dom);
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