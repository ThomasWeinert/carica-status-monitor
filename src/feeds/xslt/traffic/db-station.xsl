<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  xmlns:str="http://exslt.org/strings"
  extension-element-prefixes="date func str"
>

<xsl:import href="../functions/contains-token.xsl"/>
<xsl:import href="../functions/string.xsl"/>

<xsl:strip-space elements="*"/>

<xsl:template match="/*">
  <xsl:variable name="currentDateString" select="//body/table[1]//td"/>
  <xsl:variable name="currentDate" select="date:date-time()"/>
  <atom:feed>
    <atom:title><xsl:value-of select=".//div[func:contains-token(@class, 'rline')]"/></atom:title>
    <atom:id>urn:db/departures</atom:id>
    <atom:updated><xsl:value-of select="$currentDate"/></atom:updated>
    <xsl:for-each select=".//div[func:contains-token(@class, 'sqdetailsDep')]">
      <atom:entry>
        <xsl:variable name="route" select="normalize-space(a/span)"/>
        <xsl:variable name="details" select="span"/>
        <xsl:variable name="time" select="$details[1]/text()"/>
        <xsl:variable name="info" select="$details[2]/text()"/>
        <xsl:variable name="texts" select="text()"/>
        <xsl:variable name="direction" select="$texts[1]"/>
        <xsl:variable name="platform" select="normalize-space(substring($texts[last()], 2))"/>
        
        <atom:link rel="alternate" type="text/html" href="{a/@href}"/>
        <atom:id>urn:db/departures/<xsl:value-of select="$route"/>/<xsl:value-of select="$direction"/></atom:id>
        <atom:updated><xsl:value-of select="$currentDate"/></atom:updated>
        <atom:title>
          <xsl:value-of select="$time"/>
          <xsl:text>, </xsl:text>
          <xsl:value-of select="$route"/>
          <xsl:text> </xsl:text>
          <xsl:value-of select="$direction"/>
        </atom:title>
        <atom:summary>
          <xsl:value-of select="$platform"/>
          <xsl:if test="$info != ''">
            <xsl:text>, </xsl:text>
            <xsl:value-of select="$info"/>
          </xsl:if>
        </atom:summary>
        <xsl:if test="span[func:contains-token(@class, 'red')]">
          <csm:status>error</csm:status>
        </xsl:if>
        <xsl:call-template name="image-by-route">
          <xsl:with-param name="route" select="$route"/>
        </xsl:call-template>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<xsl:template name="image-by-route">
  <xsl:param name="route"/>
  <xsl:param name="type" select="str:upper(substring-before(translate($route, '1234567890', '          '), ' '))"/>
  <xsl:variable name="vehicles" select="document('./vehicles.xml')/vehicles"/>
  <xsl:choose>
    <xsl:when test="$vehicles/vehicle[@code = $type]">
      <xsl:variable name="vehicle" select="$vehicles/vehicle[@code = $type]"/>
      <csm:icon src="img/{$vehicle/@image}" title="{$route}"/>
    </xsl:when>
    <xsl:otherwise>
      <csm:icon src="img/traffic-sprite-train.png" title="{$route}"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>