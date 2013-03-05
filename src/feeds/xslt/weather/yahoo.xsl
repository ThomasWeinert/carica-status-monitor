<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  xmlns:yweather="http://xml.weather.yahoo.com/ns/rss/1.0"
  extension-element-prefixes="date func"
>

<xsl:import href="../rss/dates.xsl"/>

<xsl:param name="FEED_PATH"></xsl:param>

<xsl:template match="/*">
  <atom:feed>
    <atom:title><xsl:value-of select="channel/title"/></atom:title>
    <atom:link href="{channel/link}"/>
    <atom:id><xsl:value-of select="channel/link"/></atom:id>
    <atom:updated><xsl:value-of select="date:convertDateRssToAtom(channel/lastBuildDate)"/></atom:updated>
    <xsl:copy-of select="channel/yweather:*"/>
    <xsl:for-each select="channel/item">
      <atom:entry>
        <atom:id><xsl:value-of select="guid"/></atom:id>
        <atom:updated><xsl:value-of select="date:convertDateRssToAtom(pubDate)"/></atom:updated>
        <atom:link rel="alternate" href="{link}"/>
        <atom:link rel="image" href="{csm:weather-image(yweather:condition/@code)}"/>
        <atom:title><xsl:value-of select="title"/></atom:title>
        <atom:summary>
          <xsl:value-of select="yweather:condition/@text"/>
          <xsl:text>, </xsl:text>
          <xsl:value-of select="yweather:condition/@temp"/>°
          <xsl:value-of select="../yweather:units/@temperature"/>
        </atom:summary>
        <xsl:copy-of select="yweather:*"/>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<func:function name="csm:weather-image">
  <xsl:param name="code">3200</xsl:param>
  <xsl:variable name="conditions" select="document('conditions.xml')/*/condition"/>
  <xsl:variable name="image">
    <xsl:choose>
      <xsl:when test="$conditions[@code = $code]">
        <xsl:value-of select="$conditions[@code = $code]/@icon"/>
      </xsl:when>
      <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <func:result>
    <xsl:choose>
      <xsl:when test="$image != ''">
        <xsl:value-of select="$FEED_PATH"/>
        <xsl:text>../img/</xsl:text>
        <xsl:value-of select="$image"/>
      </xsl:when>
      <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
  </func:result>
</func:function>

</xsl:stylesheet>