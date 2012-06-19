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
  <!--  Tue, 19 Jun 2012 6:19 pm CEST -->
  <xsl:variable name="weekDay" select="substring-before($rssDate, ', ')"/>
  <xsl:variable name="withoutWeekDay">
    <xsl:choose>
      <xsl:when test="contains($rssDate, ', ')">
        <xsl:value-of select="substring-after($rssDate, ', ')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$rssDate"/>
      </xsl:otherwise>
    </xsl:choose> 
  </xsl:variable>
  <xsl:variable name="day" select="substring-before($withoutWeekDay, ' ')"/> 
  <xsl:variable name="withoutDay" select="substring-after($withoutWeekDay, ' ')"/> 
  <xsl:variable name="month" select="substring-before($withoutDay, ' ')"/> 
  <xsl:variable name="withoutMonth" select="substring-after($withoutDay, ' ')"/> 
  <xsl:variable name="year" select="substring-before($withoutMonth, ' ')"/> 
  <xsl:variable name="withoutYear" select="substring-after($withoutMonth, ' ')"/> 
  <xsl:variable name="hour" select="substring-before($withoutYear, ':')"/> 
  <xsl:variable name="withoutHour" select="substring-after($withoutYear, ':')"/>
  <xsl:variable name="minute">
    <xsl:choose>
      <xsl:when test="contains($withoutHour, ':')">
        <xsl:value-of select="substring-before($withoutHour, ':')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="substring-before($withoutHour, ' ')"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable> 
  <xsl:variable name="withoutMinute">
    <xsl:choose>
      <xsl:when test="contains($withoutHour, ':')">
        <xsl:value-of select="substring-after($withoutHour, ':')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="substring-after($withoutHour, ' ')"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="seconds">
    <xsl:choose>
      <xsl:when test="contains($withoutHour, ':')">
        <xsl:value-of select="substring-before($withoutMinute, ' ')"/>
      </xsl:when>
      <xsl:otherwise>0</xsl:otherwise>
    </xsl:choose>
  </xsl:variable> 
  <xsl:variable name="withoutSeconds">
    <xsl:choose>
      <xsl:when test="contains($withoutHour, ':')">
        <xsl:value-of select="substring-after($withoutHour, ' ')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$withoutMinute"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="amPm" select="substring-before($withoutSeconds, ' ')"/> 
  <xsl:variable name="zone" select="substring-after($withoutSeconds, ' ')"/> 
  <xsl:variable name="result">
    <xsl:choose>
      <xsl:when test="$year &lt; 100">
        <xsl:value-of select="2000 + $year"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$year"/>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:text>-</xsl:text>
    <xsl:choose>
      <xsl:when test="$month = 'Jan'">01</xsl:when>
      <xsl:when test="$month = 'Feb'">02</xsl:when>
      <xsl:when test="$month = 'Mar'">03</xsl:when>
      <xsl:when test="$month = 'Apr'">04</xsl:when>
      <xsl:when test="$month = 'May'">05</xsl:when>
      <xsl:when test="$month = 'Jun'">06</xsl:when>
      <xsl:when test="$month = 'Jul'">07</xsl:when>
      <xsl:when test="$month = 'Aug'">08</xsl:when>
      <xsl:when test="$month = 'Sep'">09</xsl:when>
      <xsl:when test="$month = 'Oct'">10</xsl:when>
      <xsl:when test="$month = 'Nov'">11</xsl:when>
      <xsl:when test="$month = 'Dec'">12</xsl:when>
    </xsl:choose>
    <xsl:text>-</xsl:text>
    <xsl:value-of select="format-number($day, '00')"/>
    <xsl:text>T</xsl:text>
    <xsl:choose>
      <xsl:when test="$hour = 12 and $amPm = 'pm'">
        <xsl:text>12</xsl:text>
      </xsl:when>
      <xsl:when test="$hour = 12 and $amPm = 'am'">
        <xsl:text>0</xsl:text>
      </xsl:when>
      <xsl:when test="$amPm = 'pm'">
        <xsl:value-of select="format-number(12 + $hour, '00')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="format-number($hour, '00')"/>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:text>:</xsl:text>
    <xsl:value-of select="format-number($minute, '00')"/>
    <xsl:text>:</xsl:text>
    <xsl:value-of select="format-number($seconds, '00')"/>
    <xsl:choose>
      <!--- Yeah! timezones imcomplete list of course-->
      <xsl:when test="$zone = 'Z'">Z</xsl:when>
      <xsl:when test="$zone = 'UT'">Z</xsl:when>
      <xsl:when test="$zone = 'GMT'">Z</xsl:when>
      <xsl:when test="$zone = 'EST'">-0500</xsl:when>
      <xsl:when test="$zone = 'EDT'">-0400</xsl:when>
      <xsl:when test="$zone = 'CST'">-0600</xsl:when>
      <xsl:when test="$zone = 'CDT'">-0500</xsl:when>
      <xsl:when test="$zone = 'MST'">-0700</xsl:when>
      <xsl:when test="$zone = 'MDT'">-0600</xsl:when>
      <xsl:when test="$zone = 'PST'">-0800</xsl:when>
      <xsl:when test="$zone = 'PDT'">-0700</xsl:when>
      <xsl:when test="$zone = 'CEST'">+0200</xsl:when>
      <xsl:when test="$zone = 'CET'">+0100</xsl:when>
      <xsl:when test="$zone = 'A'">-0100</xsl:when>
      <xsl:when test="$zone = 'M'">-1200</xsl:when>
      <xsl:when test="$zone = 'N'">+0100</xsl:when>
      <xsl:when test="$zone = 'Y'">+1200</xsl:when>
      <xsl:when test="contains($zone, '+') or contains($zone, '-')">
        <xsl:value-of select="$zone"/>
      </xsl:when>
    </xsl:choose>
  </xsl:variable>
  <func:result select="$result"/>
</func:function>

</xsl:stylesheet>