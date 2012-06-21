<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="date func"
>

<xsl:template match="/*">
  <xsl:variable name="now" select="date:date-time()"/>
  <atom:feed>
    <xsl:for-each select="event">
      <xsl:sort select="data[@name='DTSTART']/value" data-type="text" order="ascending"/>
      <xsl:variable name="startDate" select="date:parseIcalDateTime(data[@name='DTSTART']/value, data[@name='DTSTART']/parameter/@value)"/>
      <xsl:if test="date:difference($startDate, $now) != 0">
        <atom:entry>
          <atom:title><xsl:value-of select="data[@name='SUMMARY']/value"/></atom:title>
          <atom:id><xsl:value-of select="data[@name='URL']/value"/></atom:id>
          <atom:updated><xsl:value-of select="$now"/></atom:updated>
          <atom:summary>
            <xsl:value-of select="data[@name='LOCATION']/value"/>
          </atom:summary>
          <csm:event-start-time><xsl:value-of select="$startDate"/></csm:event-start-time>
          <csm:icon src="img/calendar.png"/>
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
      <xsl:when test="$format = 'DATE-TIME'">
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