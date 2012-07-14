<?php

namespace Carica\StatusMonitor\Library\Source {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class JsonHtmlTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::__construct
     */
    public function testConstructorWithAllParameters() {
      $source = new JsonHtml('http://example.tld/sample-data.json', 'sample.property', 23);
      $this->assertAttributeEquals(
        'http://example.tld/sample-data.json', '_url', $source
      );
      $this->assertAttributeEquals(
        23, '_timeout', $source
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::read
     */
    public function testReadExpectingDom() {
      $source = new JsonHtml('http://example.tld/sample-data.json', 'sample.property');
      $this->assertInstanceOf(
        '\DOMDocument', $dom = $source->read()
      );
      $this->assertEquals(
        '<html><body><div>success</div></body></html>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Url::read
     */
    public function testReadExpectingNull() {
      $source = new JsonHtml('http://example.tld/INVALID.json', 'sample.property');
      $this->assertNull(
        $source->read()
      );
    }
  }
}