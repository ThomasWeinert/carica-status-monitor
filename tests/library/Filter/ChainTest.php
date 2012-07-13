<?php

namespace Carica\StatusMonitor\Library\Filter {

  require_once(__DIR__.'/../TestCase.php');

  class ChainTest extends \Carica\StatusMonitor\Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Filter\Chain
     */
    public function testFilterWithTwoFilters() {
      $dom = $this->getMock('DOMDocument');
      $filterOne = $this->getMock('Carica\StatusMonitor\Library\Filter');
      $filterOne
        ->expects($this->once())
        ->method('filter')
        ->with($this->isInstanceOf('DOMDocument'))
        ->will($this->returnValue($dom));
      $filterTwo = $this->getMock('Carica\StatusMonitor\Library\Filter');
      $filterTwo
        ->expects($this->once())
        ->method('filter')
        ->with($this->isInstanceOf('DOMDocument'))
        ->will($this->returnValue($dom));
      $filter = new Chain($filterOne, $filterTwo);
      $this->assertInstanceOf('DOMDocument', $filter->filter($dom));
    }
  }
}
