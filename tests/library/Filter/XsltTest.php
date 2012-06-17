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
  }
}