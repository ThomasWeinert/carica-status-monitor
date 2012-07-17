<?php

namespace Carica\StatusMonitor\Library\Source {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class HtmlPageTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Source\HtmlPage::__construct
     */
    public function testConstructorWithAllParameters() {
      $source = new HtmlPage('http://example.tld/success.html');
      $this->assertAttributeEquals(
        'http://example.tld/success.html', '_url', $source
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\HtmlPage::read
     */
    public function testReadExpectingDom() {
      $source = new HtmlPage('http://example.tld/sample.html');
      $this->assertInstanceOf(
        '\DOMDocument', $source->read()
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\HtmlPage::read
     */
    public function testReadExpectingNull() {
      $source = new HtmlPage('http://example.tld/INVALID.html');
      $this->assertNull(
        $source->read()
      );
    }
  }
}