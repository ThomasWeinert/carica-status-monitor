<?php

namespace Carica\StatusMonitor\Library\File {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class ResourceIteratorTest extends Library\TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\File\ResourceIterator::__construct
     */
    public function testConstructor() {
      $iterator = new ResourceIterator($fh = $this->getMemoryStream('foo'));
      $this->assertAttributeEquals(
        $fh, '_resource', $iterator
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\File\ResourceIterator::__construct
     */
    public function testConstructorWithOptions() {
      $iterator = new ResourceIterator(
        $fh = $this->getMemoryStream('foo'),
        FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES
      );
      $this->assertTrue(
        $iterator->isIgnoringNewLines()
      );
      $this->assertTrue(
        $iterator->isSkippingEmptyLines()
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\File\ResourceIterator
     */
    public function testIteration() {
      $iterator = new ResourceIterator($this->getMemoryStream("foo\r\nbar"));
      $this->assertEquals(
        array("foo\r\n", 'bar'),
        iterator_to_array($iterator)
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\File\ResourceIterator
     */
    public function testIterationWithEmptyLine() {
      $iterator = new ResourceIterator(
        $this->getMemoryStream("foo\r\n\r\nbar")
      );
      $this->assertEquals(
        array("foo\r\n", "\r\n", 'bar'),
        iterator_to_array($iterator)
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\File\ResourceIterator
     */
    public function testIterationIgnoringEmptyLines() {
      $iterator = new ResourceIterator(
        $this->getMemoryStream("foo\r\n\r\nbar"),
        FILE_SKIP_EMPTY_LINES
      );
      $this->assertEquals(
        array("foo\r\n", 'bar'),
        iterator_to_array($iterator)
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\File\ResourceIterator
     */
    public function testIterationTrimLineFeeds() {
      $iterator = new ResourceIterator(
        $this->getMemoryStream("foo\r\nbar"),
        FILE_IGNORE_NEW_LINES
      );
      $this->assertEquals(
        array("foo", 'bar'),
        iterator_to_array($iterator)
      );
    }

    /**
     * Fixture method return an resource handle for the string
     * 
     * @param string $data
     * @return resource
     */
    private function getMemoryStream($data) {
      return fopen('data://text/plain,'.$data, 'r');
    }
  }
}