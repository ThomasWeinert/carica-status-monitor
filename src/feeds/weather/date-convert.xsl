<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="date func"
>

<!-- 
  Convert a RFC822 date time string into a RFC3339 one.

  Wed, 20 Jun 2012 12:50 am CEST
 -->
<func:function name="date:convertDateRssToAtom">
  <xsl:param name="rssDate"/>
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
        <xsl:text>00</xsl:text>
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
      <!--- Yeah! timezones incomplete list of course-->
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