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

<xsl:import href="date-convert.xsl"/>

<xsl:template match="/*">
  <xsl:variable name="information" select="weather/forecast_information"/>
  <xsl:variable name="current" select="weather/current_conditions"/>
  <atom:feed>
    <atom:title>Google Weather</atom:title>
    <atom:id>urn:google/weather/<xsl:value-of select="$information/postal_code/@data"/></atom:id>
    <atom:updated><xsl:value-of select="date:date-time()"/></atom:updated>
    <xsl:if test="$current/condition/@data != ''">
      <atom:entry>
        <atom:id>urn:google/weather/<xsl:value-of select="$information/postal_code/@data"/>/<xsl:value-of select="$information/forecast_date/@data"/></atom:id>
        <atom:updated><xsl:value-of select="date:date-time()"/></atom:updated>
        <atom:link rel="image" href="http://www.google.com/{$current/icon/@data}"/>
        <atom:title><xsl:value-of select="$information/city/@data"/></atom:title>
        <atom:summary>
          <xsl:value-of select="$current/condition/@data"/>
          <xsl:text>, </xsl:text>
          <xsl:value-of select="$current/temp_c/@data"/>
          <xsl:text>°C, </xsl:text>
          <xsl:value-of select="$current/humidity/@data"/>
          <xsl:text>, </xsl:text>
          <xsl:value-of select="$current/wind_condition/@data"/>
        </atom:summary>
      </atom:entry>
    </xsl:if>
  </atom:feed>
</xsl:template>

</xsl:stylesheet>