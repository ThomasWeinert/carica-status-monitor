<?php

namespace Carica\StatusMonitor\Library\Cache\Service {

  require_once(__DIR__.'/../../TestCase.php');

  use Carica\StatusMonitor\Library as Library;

  class FileTest extends Library\TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::__construct
     */
    public function testConstructor() {
      $configuration = $this->getMock(
        '\Carica\StatusMonitor\Library\Cache\Configuration'
      );
      $configuration
        ->expects($this->once())
        ->method('get')
        ->will(
          $this->returnValueMap(
            array(
              array('PATH', '', '/some/path')
            )
          )
        );
      $service = new File('bucket', $configuration);
      $this->assertAttributeEquals(
        'bucket', '_bucket', $service
      );
      $this->assertAttributeEquals(
        '/some/path', '_directory', $service
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::__construct
     */
    public function testConstructorWithInvalidBucketExpectingException() {
      $configuration = $this->getMock(
        '\Carica\StatusMonitor\Library\Cache\Configuration'
      );
      $this->setExpectedException('UnexpectedValueException');
      $service = new File('   ', $configuration);
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::isUseable
     */
    public function testIsUseableExpectingTrue() {
      $directory = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\Directory'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $directory
        ->expects($this->once())
        ->method('exists')
        ->will($this->returnValue(TRUE));
      $directory
        ->expects($this->once())
        ->method('isReadable')
        ->will($this->returnValue(TRUE));
      $directory
        ->expects($this->once())
        ->method('isWriteable')
        ->will($this->returnValue(TRUE));
      $factory = $this->getMock(
        '\Carica\StatusMonitor\Library\FileSystem\Factory'
      );
      $factory
        ->expects($this->once())
        ->method('getDirectory')
        ->with('/some/path')
        ->will($this->returnValue($directory));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($factory);
      $this->assertTrue($service->isUseable());
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::isUseable
     */
    public function testIsUseableDirectoryDoesNotExistsExpectingException() {
      $directory = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\Directory'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $directory
        ->expects($this->once())
        ->method('exists')
        ->will($this->returnValue(FALSE));
      $factory = $this->getMock(
        '\Carica\StatusMonitor\Library\FileSystem\Factory'
      );
      $factory
        ->expects($this->once())
        ->method('getDirectory')
        ->with('/some/path')
        ->will($this->returnValue($directory));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($factory);
      $this->setExpectedException(
        'LogicException', 'Cache directory "/some/path" not found.'
      );
      $this->assertTrue($service->isUseable());
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::isUseable
     */
    public function testIsUseableDirectoryisNotReadableExpectingException() {
      $directory = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\Directory'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $directory
        ->expects($this->once())
        ->method('exists')
        ->will($this->returnValue(TRUE));
      $directory
        ->expects($this->once())
        ->method('isReadable')
        ->will($this->returnValue(FALSE));
      $factory = $this->getMock(
        '\Carica\StatusMonitor\Library\FileSystem\Factory'
      );
      $factory
        ->expects($this->once())
        ->method('getDirectory')
        ->with('/some/path')
        ->will($this->returnValue($directory));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($factory);
      $this->setExpectedException(
        'LogicException', 'Cache directory "/some/path" not readable.'
      );
      $this->assertTrue($service->isUseable());
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::isUseable
     */
    public function testIsUseableDirectoryIsNotWriteableExpectingException() {
      $directory = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\Directory'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $directory
        ->expects($this->once())
        ->method('exists')
        ->will($this->returnValue(TRUE));
      $directory
        ->expects($this->once())
        ->method('isReadable')
        ->will($this->returnValue(TRUE));
      $directory
        ->expects($this->once())
        ->method('isWriteable')
        ->will($this->returnValue(FALSE));
      $factory = $this->getMock(
        '\Carica\StatusMonitor\Library\FileSystem\Factory'
      );
      $factory
        ->expects($this->once())
        ->method('getDirectory')
        ->with('/some/path')
        ->will($this->returnValue($directory));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($factory);
      $this->setExpectedException(
        'LogicException', 'Cache directory "/some/path" not writeable.'
      );
      $this->assertTrue($service->isUseable());
    }
  }
}