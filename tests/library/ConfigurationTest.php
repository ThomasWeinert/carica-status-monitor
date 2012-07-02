<?php

namespace Carica\StatusMonitor\Library {

  require_once(__DIR__.'/TestCase.php');

  class ConfigurationTest extends TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::__construct
     */
    public function testConstructor() {
      $configuration = new Configuration_TestProxy(
        array(
          'STRING' => 'success',
          'INTEGER' => 42
        )
      );
      $this->assertEquals(
        array(
          'STRING' => 'success',
          'INTEGER' => 42
        ),
        iterator_to_array($configuration)
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::get
     */
    public function testGet() {
      $configuration = new Configuration_TestProxy();
      $this->assertEquals('sample', $configuration->get('STRING'));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::get
     */
    public function testGetReturningDefaultValue() {
      $configuration = new Configuration_TestProxy();
      $this->assertEquals('default', $configuration->get('non_existing', 'default'));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetExists
     */
    public function testOffsetExistsExpectingTrue() {
      $configuration = new Configuration_TestProxy();
      $this->assertTrue(isset($configuration['STRING']));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetExists
     */
    public function testOffsetExistsExpectingFalse() {
      $configuration = new Configuration_TestProxy();
      $this->assertFalse(isset($configuration['INVALID_OPTION']));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetGet
     */
    public function testOffsetGet() {
      $configuration = new Configuration_TestProxy();
      $this->assertEquals('sample', $configuration['STRING']);
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetGet
     */
    public function testOffsetGetReturnsDefault() {
      $configuration = new Configuration_TestProxy();
      $this->assertEquals(
        'default', $configuration[array('INVALID_OPTIONS', 'default')]
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetSet
     */
    public function testOffsetSet() {
      $configuration = new Configuration_TestProxy();
      $configuration['STRING'] = 'success';
      $this->assertEquals('success', $configuration['STRING']);
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetSet
     */
    public function testOffsetSetConvertsType() {
      $configuration = new Configuration_TestProxy();
      $configuration['INTEGER'] = '42';
      $this->assertSame(42, $configuration['INTEGER']);
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetSet
     */
    public function testOffsetSetWithInvalidOptionExpectingException() {
      $configuration = new Configuration_TestProxy();
      $this->setExpectedException('InvalidArgumentException');
      $configuration['INVALID_OPTION'] = 'Trigger Exception';
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::offsetUnset
     */
    public function testOffsetUnset() {
      $configuration = new Configuration_TestProxy();
      unset($configuration['INTEGER']);
      $this->assertSame(0, $configuration['INTEGER']);
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Configuration::assign
     * @covers \Carica\StatusMonitor\Library\Configuration::getIterator
     */
    public function testAssign() {
      $configuration = new Configuration_TestProxy();
      $configuration->assign(
        array(
          'STRING' => 'success',
          'INTEGER' => 42,
          'INVALID_OPTION' => 'Ignored'
        )
      );
      $this->assertEquals(
        array(
          'STRING' => 'success',
          'INTEGER' => 42
        ),
        iterator_to_array($configuration)
      );
    }
  }

  class Configuration_TestProxy extends Configuration {

    protected $_options = array(
      'STRING' => 'sample',
      'INTEGER' => 21
    );
  }
}