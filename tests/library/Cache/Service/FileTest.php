<?php

namespace Carica\StatusMonitor\Library\Cache\Service {

  require_once(__DIR__.'/../../TestCase.php');

  use Carica\StatusMonitor\Library as Library;

  class FileTest extends Library\TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::__construct
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::validateName
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
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::validateName
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
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory($directory));
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
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory($directory));
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
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory($directory));
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
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory($directory));
      $this->setExpectedException(
        'LogicException', 'Cache directory "/some/path" not writeable.'
      );
      $this->assertTrue($service->isUseable());
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::read
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::getFile
     */
    public function testRead() {
      $file = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\File'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $file
        ->expects($this->once())
        ->method('isReadable')
        ->will($this->returnValue(TRUE));
      $file
        ->expects($this->once())
        ->method('modified')
        ->will($this->returnValue(time()));
      $file
        ->expects($this->once())
        ->method('read')
        ->will($this->returnValue(serialize('success')));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory(NULL, $file));
      $this->assertEquals('success', $service->read('foo', NULL, 1800));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::read
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::getFile
     */
    public function testReadExpiredExpectingNull() {
      $file = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\File'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $file
        ->expects($this->once())
        ->method('isReadable')
        ->will($this->returnValue(TRUE));
      $file
        ->expects($this->once())
        ->method('modified')
        ->will($this->returnValue(time() - 1800));
      $file
        ->expects($this->never())
        ->method('read');
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory(NULL, $file));
      $this->assertNull($service->read('foo', NULL, 900));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::write
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::getDirectory
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::getFile
     */
    public function testWrite() {
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
      $directory
        ->expects($this->once())
        ->method('force')
        ->will($this->returnValue(TRUE));
      $file = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\File'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $file
        ->expects($this->once())
        ->method('write')
        ->with(serialize('success'))
        ->will($this->returnValue(TRUE));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory($directory, $file));
      $this->assertNull($service->write('foo', NULL, 900, 'success'));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::invalidate
     */
    public function testInvalidate() {
      $file = $this
        ->getMockBuilder(
          '\Carica\StatusMonitor\Library\FileSystem\File'
        )
        ->disableOriginalConstructor()
        ->getMock();
      $file
        ->expects($this->once())
        ->method('delete')
        ->will($this->returnValue(TRUE));
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($this->getFileSystemFactory(NULL, $file));
      $this->assertNull($service->invalidate('foo', NULL));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::fileSystem
     */
    public function testFileSystemGetAfterSet() {
      $factory = $this->getFileSystemFactory();
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $service->fileSystem($factory);
      $this->assertSame($factory, $service->fileSystem());
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Service\File::fileSystem
     */
    public function testFileSystemGetImplicitCreate() {
      $service = new File(
        'bucket', new Library\Cache\Configuration(array('PATH' => '/some/path'))
      );
      $this->assertInstanceOf(
        '\Carica\StatusMonitor\Library\FileSystem\Factory',
        $service->fileSystem()
      );
    }

    private function getFileSystemFactory($directory = NULL, $file = NULL) {
      $factory = $this->getMock(
        '\Carica\StatusMonitor\Library\FileSystem\Factory'
      );
      if ($directory) {
        $factory
          ->expects($this->any())
          ->method('getDirectory')
          ->withAnyParameters()
          ->will($this->returnValue($directory));
      }
      if ($file) {
        $factory
          ->expects($this->any())
          ->method('getFile')
          ->withAnyParameters()
          ->will($this->returnValue($file));
      }
      return $factory;
    }
  }
}