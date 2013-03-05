<?php

namespace Carica\StatusMonitor\Library\Filter {

  require_once(__DIR__.'/../TestCase.php');

  class ExpandTest extends \Carica\StatusMonitor\Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Filter\Expand
     */
    public function testFilter() {
      $details = new \DOMDocument();
      $details->loadXml('<detail>success</detail>');
      $dom = new \DOMDocument();
      $dom->loadXml('<sample url="#detail"/>');

      $source = $this
        ->getMockBuilder('Carica\StatusMonitor\Library\Source\Url')
        ->disableOriginalConstructor()
        ->getMock();
      $source
        ->expects($this->once())
        ->method('setUrl')
        ->with('#detail');
      $source
        ->expects($this->once())
        ->method('read')
        ->will($this->returnValue($details));

      $filter = new Expand('//sample', 'string(@url)');
      $filter->source($source);
      $dom = $filter->filter($dom);
      $this->assertEquals(
        '<sample url="#detail"><detail>success</detail></sample>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Filter\Expand
     */
    public function testFilterWithNamespace() {
      $details = new \DOMDocument();
      $details->loadXml('<detail xmlns="urn:a">success</detail>');
      $dom = new \DOMDocument();
      $dom->loadXml('<sample xmlns="urn:a" url="#detail"/>');

      $source = $this
        ->getMockBuilder('Carica\StatusMonitor\Library\Source\Url')
        ->disableOriginalConstructor()
        ->getMock();
      $source
        ->expects($this->once())
        ->method('setUrl')
        ->with('#detail');
      $source
        ->expects($this->once())
        ->method('read')
        ->will($this->returnValue($details));

      $filter = new Expand('//b:sample', 'string(@url)', array('b' => 'urn:a'));
      $filter->source($source);
      $dom = $filter->filter($dom);
      $this->assertEquals(
        '<sample xmlns="urn:a" url="#detail"><detail>success</detail></sample>',
        $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Filter\Expand::Source
     */
    public function testSourceGetAfterSet() {
      $filter = new Expand('');
      $filter->source(
        $source = $this
          ->getMockBuilder('Carica\StatusMonitor\Library\Source\Url')
          ->disableOriginalConstructor()
          ->getMock()
      );
      $this->assertSame($source, $filter->source());
    }

    /**
     * @covers Carica\StatusMonitor\Library\Filter\Expand::Source
     */
    public function testSourceGetImplicitCreate() {
      $filter = new Expand('');
      $this->assertInstanceOf(
        'Carica\StatusMonitor\Library\Source\Url', $filter->source()
      );
    }
  }
}
