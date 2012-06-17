<?php

namespace Carica\StatusMonitor\Library\Filter {

  require_once(__DIR__.'/../TestCase.php');

  class XsltTest extends \Carica\StatusMonitor\Library\TestCase {

    /**
     * @covers Xslt::__construct
     */
    public function testConstructor() {
      $filter = new Xslt('success.xsl');
      $this->assertAttributeEquals(
        'success.xsl', '_xsltFile', $filter
      );
    }

    /**
     * @covers Xslt::filter
     */
    public function testFilter() {
      $filter = new Xslt(__DIR__.'/TestData/sample.xsl');
      $input = new \DOMDocument();
      $input->appendChild($input->createElement('data'));
      $output = $filter->filter($input);
      $this->assertEquals(
        '<success/>', $output->saveXml($output->documentElement)
      );
    }

    /**
     * @covers Xslt::processor
     */
    function testProcessorGetAfterSet() {
      $processor = $this->getMock('\XsltProcessor');
      $filter = new Xslt('sample.xsl');
      $filter->processor($processor);
      $this->assertSame(
        $processor, $filter->processor()
      );
    }

    /**
     * @covers Xslt::processor
     */
    function testProcessorGetImplicitCreate() {
      $filter = new Xslt('sample.xsl');
      $this->assertInstanceOf(
        '\XsltProcessor', $filter->processor()
      );
    }
  }
}