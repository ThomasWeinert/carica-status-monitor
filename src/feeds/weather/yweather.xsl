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

<xsl:template match="/*">
  <atom:feed>
    <atom:title><xsl:value-of select="channel/title"/></atom:title>
    <atom:link href="{channel/link}"/>
    <atom:id><xsl:value-of select="channel/link"/></atom:id>
    <atom:updated><xsl:value-of select="csm:convertRssDate(channel/lastBuildDate)"/></atom:updated>
    <xsl:copy-of select="channel/yweather:*"/>
    <xsl:for-each select="channel/item">
      <atom:entry>
        <atom:id><xsl:value-of select="guid"/></atom:id>
        <atom:updated><xsl:value-of select="csm:convertRssDate(pubDate)"/></atom:updated>
        <atom:link rel="alternate" href="{link}"/>
        <atom:link rel="image" href="{csm:weather-image(yweather:condition/@code)}"/>
        <atom:title><xsl:value-of select="title"/></atom:title>
        <atom:summary>
          <xsl:value-of select="yweather:condition/@text"/>
          <xsl:text>, </xsl:text>
          <xsl:value-of select="yweather:condition/@temp"/>          
          <xsl:value-of select="../yweather:units/@temperature"/>
        </atom:summary>
        <xsl:copy-of select="yweather:*"/>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<func:function name="csm:weather-image">
  <xsl:param name="code">3200</xsl:param>
  <xsl:variable name="image">
    <xsl:choose>
      <!--tornado-->
      <xsl:when test="$code = 0">weather-severe-alert.png</xsl:when>
      <!--tropical storm-->
      <xsl:when test="$code = 1">weather-severe-alert.png</xsl:when>
      <!--hurricane-->
      <xsl:when test="$code = 2">weather-severe-alert.png</xsl:when>
      <!--severe thunderstorms-->
      <xsl:when test="$code = 3">weather-severe-alert.png</xsl:when>
      <!--thunderstorms-->
      <xsl:when test="$code = 4">weather-severe-alert.png</xsl:when>
      <!--mixed rain and snow-->
      <xsl:when test="$code = 5">weather-snow.png</xsl:when>
      <!--mixed rain and sleet-->
      <xsl:when test="$code = 6">weather-snow.png</xsl:when>
      <!--mixed snow and sleet-->
      <xsl:when test="$code = 7">weather-snow.png</xsl:when>
      <!--freezing drizzle-->
      <xsl:when test="$code = 8">weather-showers-scattered.png</xsl:when>
      <!--drizzle-->
      <xsl:when test="$code = 9">weather-showers-scattered.png</xsl:when>
      <!--freezing rain-->
      <xsl:when test="$code = 10">weather-showers.png</xsl:when>
      <!--showers-->
      <xsl:when test="$code = 11">weather-showers.png</xsl:when>
      <!--showers-->
      <xsl:when test="$code = 12">weather-showers.png</xsl:when>
      <!--snow flurries-->
      <xsl:when test="$code = 13">weather-snow.png</xsl:when>
      <!--light snow showers-->
      <xsl:when test="$code = 14">weather-snow.png</xsl:when>
      <!--blowing snow-->
      <xsl:when test="$code = 15">weather-snow.png</xsl:when>
      <!--snow-->
      <xsl:when test="$code = 16">weather-snow.png</xsl:when>
      <!--hail-->
      <xsl:when test="$code = 17">weather-severe-alert.png</xsl:when>
      <!--sleet-->
      <xsl:when test="$code = 18">weather-severe-alert.png</xsl:when>
      <!--dust-->
      <xsl:when test="$code = 19"></xsl:when>
      <!--foggy-->
      <xsl:when test="$code = 20"></xsl:when>
      <!--haze-->
      <xsl:when test="$code = 21">weather-severe-alert.png</xsl:when>
      <!--smoky-->
      <xsl:when test="$code = 22">weather-severe-alert.png</xsl:when>
      <!--blustery-->
      <xsl:when test="$code = 23">weather-severe-alert.png</xsl:when>
      <!--windy-->
      <xsl:when test="$code = 24"></xsl:when>
      <!--cold-->
      <xsl:when test="$code = 25"></xsl:when>
      <!--cloudy-->
      <xsl:when test="$code = 26">weather-overcast.png</xsl:when>
      <!--mostly cloudy (night)-->
      <xsl:when test="$code = 27"></xsl:when>
      <!--mostly cloudy (day)-->
      <xsl:when test="$code = 28"></xsl:when>
      <!--partly cloudy (night)-->
      <xsl:when test="$code = 29"></xsl:when>
      <!--partly cloudy (day)-->
      <xsl:when test="$code = 30">weather-few-clouds.png</xsl:when>
      <!--clear (night)-->
      <xsl:when test="$code = 31"></xsl:when>
      <!--sunny-->
      <xsl:when test="$code = 32">weather-clear.png</xsl:when>
      <!--fair (night)-->
      <xsl:when test="$code = 33"></xsl:when>
      <!--fair (day)-->
      <xsl:when test="$code = 34">weather-clear.png</xsl:when>
      <!--mixed rain and hail-->
      <xsl:when test="$code = 35">weather-showers.png</xsl:when>
      <!--hot-->
      <xsl:when test="$code = 36">weather-clear.png</xsl:when>
      <!--isolated thunderstorms-->
      <xsl:when test="$code = 37">weather-storm.png</xsl:when>
      <!--scattered thunderstorms-->
      <xsl:when test="$code = 38">weather-storm.png</xsl:when>
      <!--scattered thunderstorms-->
      <xsl:when test="$code = 39">weather-storm.png</xsl:when>
      <!--scattered showers-->
      <xsl:when test="$code = 40">weather-showers-scattered.png</xsl:when>
      <!--heavy snow-->
      <xsl:when test="$code = 41">weather-severe-alert.png</xsl:when>
      <!--scattered snow showers-->
      <xsl:when test="$code = 42">weather-snow.png</xsl:when>
      <!--heavy snow-->
      <xsl:when test="$code = 43">weather-severe-alert.png</xsl:when>
      <!--partly cloudy-->
      <xsl:when test="$code = 44">weather-few-clouds.png</xsl:when>
      <!--thundershowers-->
      <xsl:when test="$code = 45">weather-storm.png</xsl:when>
      <!--snow showers-->
      <xsl:when test="$code = 46">weather-snow.png</xsl:when>
      <!--isolated thundershowers-->
      <xsl:when test="$code = 47">weather-storm.png</xsl:when>
      <!--not available-->
      <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <func:result>
    <xsl:choose>
      <xsl:when test="$image != ''">
        <xsl:text>img/</xsl:text>
        <xsl:value-of select="$image"/>
      </xsl:when>
      <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
  </func:result>
</func:function>

<func:function name="csm:convertRssDate">
  <xsl:param name="rssDate"/>
  <xsl:variable name="result">
    <xsl:value-of select="$rssDate"/>
  </xsl:variable>
  <func:result select="$result"/>
</func:function>

</xsl:stylesheet>