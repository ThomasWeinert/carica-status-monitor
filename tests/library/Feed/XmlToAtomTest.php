<?php

namespace Carica\StatusMonitor\Library\Feed {

  require_once(__DIR__.'/../TestCase.php');

  use Carica\StatusMonitor\Library as Library;

  class XmlToAtomTest extends Library\TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Feed\XmlToAtom::__construct
     */
    public function testConstructor() {
      $feed = new XmlToAtom('http://example.tld/success.xml', 'sample.xsl');
      $this->assertAttributeEquals(
        'http://example.tld/success.xml', '_url', $feed
      );
      $this->assertAttributeEquals(
        'sample.xsl', '_xslt', $feed
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Feed\XmlToAtom::setTimeout
     */
    public function testSetTimeout() {
      $feed = new XmlToAtom('http://example.tld/success.xml');
      $feed->setTimeout(42);
      $this->assertAttributeEquals(
        42, '_timeout', $feed
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Feed\XmlToAtom::createSource
     */
    public function testCreateSource() {
      $feed = new XmlToAtom_TestProxy('http://example.tld/success.xml');
      $this->assertInstanceOf(
        'Carica\StatusMonitor\Library\Source', $feed->createSource()
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Feed\XmlToAtom::createFilter
     */
    public function testCreateFilter() {
      $feed = new XmlToAtom_TestProxy(
        'http://example.tld/success.xml', 'sample.xsl'
      );
      $this->assertInstanceOf(
        'Carica\StatusMonitor\Library\Filter', $feed->createFilter()
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Feed\XmlToAtom::createFilter
     */
    public function testCreateFilterExpectingNull() {
      $feed = new XmlToAtom_TestProxy('http://example.tld/success.xml');
      $this->assertFalse($feed->createFilter());
    }
  }

  class XmlToAtom_TestProxy extends XmlToAtom {
    public function createSource() {
      return parent::createSource();
    }
    public function createFilter() {
      return parent::createFilter();
    }
  }
}