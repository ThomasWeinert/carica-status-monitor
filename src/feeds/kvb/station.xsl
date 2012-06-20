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
  <xsl:variable name="currentDateString" select="//body/table[1]//td"/>
  <xsl:variable name="currentDate" select="date:date-time()"/>
  <atom:feed>
    <atom:title>KVB Departure Times</atom:title>
    <atom:id>urn:kvb/departures</atom:id>
    <atom:updated><xsl:value-of select="$currentDate"/></atom:updated>
    <xsl:for-each select="//body/table[position() = last()]//tr[not(starts-with(td[3], 'Sofort'))]">
      <xsl:variable name="parts" select="td"/>
      <xsl:variable name="route" select="normalize-space(translate($parts[1], '&#160;', ' '))"/>
      <xsl:variable name="destination" select="normalize-space(translate($parts[2], '&#160;', ' '))"/>
      <xsl:variable name="departureTime" select="normalize-space(translate($parts[3], '&#160;', ' '))"/>
      <xsl:variable name="departureTimeMinutes" select="substring-before($departureTime, ' Min')"/>
      <xsl:if test="($departureTimeMinutes = '') or ($departureTimeMinutes &gt; 4)">
        <atom:entry>
          <csm:icon src="img/feets.png"/>
          <atom:id>urn:kvb/departure/route/<xsl:value-of select="$route"/>/<xsl:value-of select="$destination"/></atom:id>
          <atom:updated><xsl:value-of select="$currentDate"/></atom:updated>
          <atom:title><xsl:value-of select="$route"/> - <xsl:value-of select="$destination"/></atom:title>
          <atom:summary>
            Nächste Abfahrt: <xsl:value-of select="$departureTime"/>
          </atom:summary>
        </atom:entry>
      </xsl:if>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

</xsl:stylesheet>