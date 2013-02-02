<?php

namespace Carica\StatusMonitor\Library\Source {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class IcalendarTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\Source\Icalendar
     */
    public function testReadWithSimpleFile() {
      $source = new Icalendar('');
      $source->fileIterator(
        new \ArrayIterator(
          file(__DIR__.'/TestData/simple.ical')
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<xCal:iCalendar xmlns:xCal="urn:ietf:params:xml:ns:xcal">
          <xCal:vcalendar>
            <xCal:version>2.0</xCal:version>
            <xCal:vevent>
              <xCal:dtend value="DATE-TIME">20120619T230000Z</xCal:dtend>
              <xCal:dtstart value="DATE-TIME">20120619T180000Z</xCal:dtstart>
              <xCal:description>Sample Description</xCal:description>
              <xCal:url>http://example.tld/</xCal:url>
              <xCal:summary>Example Summary</xCal:summary>
              <xCal:location>Example Location</xCal:location>
            </xCal:vevent>
          </xCal:vcalendar>
        </xCal:iCalendar>',
        $source->read()->saveXml()
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Icalendar
     */
    public function testReadWithEmptyLines() {
      $source = new Icalendar('');
      $source->fileIterator(
        new \ArrayIterator(
          array(' ')
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<xCal:iCalendar xmlns:xCal="urn:ietf:params:xml:ns:xcal"/>',
        $source->read()->saveXml()
      );
    }
    /**
     * @covers Carica\StatusMonitor\Library\Source\Icalendar
     */
    public function testReadWithInvalidLines() {
      $source = new Icalendar('');
      $source->fileIterator(
        new \ArrayIterator(
          array('Invalid')
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<xCal:iCalendar xmlns:xCal="urn:ietf:params:xml:ns:xcal"/>',
        $source->read()->saveXml()
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\Source\Icalendar::fileIterator
     */
    public function testFileIteratorImplicitCreate() {
      $source = new Icalendar('http://example.tld/hcking.de.ical');
      $this->assertInstanceOf(
        'Carica\\StatusMonitor\\Library\\File\\Iterator',
        $source->fileIterator()
      );
    }
  }
}