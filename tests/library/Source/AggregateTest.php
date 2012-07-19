<?php

namespace Carica\StatusMonitor\Library\Source {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class AggregateTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Source\Aggregate
     */
    public function testAggregateTwoFeeds() {
      $source = new Aggregate(
        array(
          $this->getAtomFeedMock(__DIR__.'/TestData/atom-one.xml'),
          $this->getAtomFeedMock(__DIR__.'/TestData/atom-two.xml')
        )
      );
      $dom = $source->read();
      $this->assertXmlStringEqualsXmlFile(
        __DIR__.'/TestData/atom-aggregated.xml',
        $dom->saveXml()
      );
    }

    private function getAtomFeedMock($file) {
      $dom = new \DOMDocument();
      $dom->load($file);
      $source = $this
        ->getMockBuilder('Carica\StatusMonitor\Library\Feed')
        ->disableOriginalConstructor()
        ->setMethods(array('get'))
        ->getMock();
      $source
        ->expects($this->once())
        ->method('get')
        ->will($this->returnValue($dom));
      return $source;
    }
  }
}

