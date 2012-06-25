<?php

namespace Carica\StatusMonitor\Library\File {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class IteratorTest extends Library\TestCase {

    /**
     * @covers Iterator::__construct
     */
    public function testConstructor() {
      $iterator = new Iterator('success.txt');
      $this->assertAttributeEquals(
        'success.txt', '_name', $iterator
      );
    }

    /**
     * @covers Iterator::getIterator
     */
    public function testGetIterator() {
      $iterator = new Iterator(__FILE__);
      $this->assertInstanceOf(
        '\Carica\StatusMonitor\Library\File\ResourceIterator',
        $iterator->getIterator()
      );
    }
  }
}