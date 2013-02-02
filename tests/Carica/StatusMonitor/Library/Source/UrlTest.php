<?php

namespace Carica\StatusMonitor\Library\Source {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class UrlTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::__construct
     */
    public function testConstructorWithAllParameters() {
      $source = new Url('http://example.tld/success.xml', 23);
      $this->assertAttributeEquals(
        'http://example.tld/success.xml', '_url', $source
      );
      $this->assertAttributeEquals(
        23, '_timeout', $source
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::setUrl
     */
    public function testSetUrl() {
      $source = new Url('', 23);
      $source->setUrl('http://example.tld/success.xml');
      $this->assertAttributeEquals(
        'http://example.tld/success.xml', '_url', $source
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::read
     */
    public function testReadExpectingDom() {
      $source = new Url('http://example.tld/sample-feed.xml');
      $this->assertInstanceOf(
        '\DOMDocument', $source->read()
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::read
     */
    public function testReadExpectingNull() {
      $source = new Url('http://example.tld/INVALID.xml');
      $this->assertNull(
        $source->read()
      );
    }
  }
}