<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:xCal="urn:ietf:params:xml:ns:xcal"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="date func"
>

<xsl:template match="/*">
  <xsl:variable name="now" select="date:date-time()"/>
  <xsl:variable name="nowTimestamp" select="date:seconds($now)"/>
  <xsl:variable name="events" select="xCal:vcalendar/xCal:vevent"/>
  <atom:feed>
    <xsl:for-each select="$events">
      <xsl:sort select="xCal:dtstart" data-type="text" order="ascending"/>
      <xsl:variable name="isFullday" select="xCal:dtstart/@value = 'DATE'"/>
      <xsl:variable name="startDate" select="date:parseIcalDateTime(xCal:dtstart, xCal:dtstart/@value)"/>
      <xsl:variable name="endDate">
        <xsl:choose>
          <xsl:when test="$isFullday">
            <xsl:value-of select="date:add(date:parseIcalDateTime(xCal:dtend, xCal:dtend/@value), 'P1D')"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="date:parseIcalDateTime(xCal:dtend, xCal:dtend/@value)"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:variable>
      <xsl:variable name="isStarted" select="date:seconds($startDate) &lt;= $nowTimestamp"/>
      <xsl:variable name="isEnded" select="date:seconds($endDate) &lt; $nowTimestamp"/>
      <xsl:if test="($isStarted and not($isEnded)) or not($isStarted)">
        <atom:entry>
          <atom:title><xsl:value-of select="xCal:summary"/></atom:title>
          <atom:id>
            <xsl:choose>
              <xsl:when test="xCal:uid"><xsl:value-of select="xCal:uid"/></xsl:when>
              <xsl:when test="xCal:url"><xsl:value-of select="xCal:url"/></xsl:when>
            </xsl:choose>
          </atom:id>
          <xsl:if test="xCal:url">
            <atom:link rel="alternate" type="text/html" href="{xCal:url}"/>
          </xsl:if>
          <atom:updated>
            <xsl:choose>
              <xsl:when test="xCal:last-modified">
                <xsl:value-of select="date:parseIcalDateTime(xCal:last-modified)"/>
              </xsl:when>
              <xsl:when test="xCal:created">
                <xsl:value-of select="date:parseIcalDateTime(xCal:created)"/>
              </xsl:when>
              <xsl:otherwise><xsl:value-of select="$now"/></xsl:otherwise>
            </xsl:choose>
          </atom:updated>
          <atom:summary>
            <xsl:value-of select="xCal:location"/>
          </atom:summary>
          <csm:icon src="img/calendar.png"/>
          <xsl:copy-of select="."/>
        </atom:entry>
      </xsl:if>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<!--  20120619T180000Z -->
<func:function name="date:parseIcalDateTime">
  <xsl:param name="dateTime"/>
  <xsl:param name="format"/>
  <xsl:variable name="result">
    <xsl:value-of select="substring($dateTime, 1, 4)"/>
    <xsl:text>-</xsl:text>
    <xsl:value-of select="substring($dateTime, 5, 2)"/>
    <xsl:text>-</xsl:text>
    <xsl:value-of select="substring($dateTime, 7, 2)"/>
    <xsl:text>T</xsl:text>
    <xsl:choose>
      <xsl:when test="$format != 'DATE'">
        <xsl:value-of select="substring($dateTime, 10, 2)"/>
        <xsl:text>:</xsl:text>
        <xsl:value-of select="substring($dateTime, 12, 2)"/>
        <xsl:text>:</xsl:text>
        <xsl:value-of select="substring($dateTime, 14, 2)"/>
        <xsl:value-of select="substring($dateTime, 16)"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>00:00:00Z</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <func:result select="$result"/>
</func:function>

</xsl:stylesheet>