<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:func="http://exslt.org/functions"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:math="http://exslt.org/math"
  extension-element-prefixes="func date math"
>

<xsl:param name="FEED_PATH"></xsl:param>
<xsl:param name="ENTRY_LIMIT" select="15"/>

<!--
  The level of detail an jenkins output depends on the "depth" parameter

  0 = only the jobs list
  1 = jobs and details about them including the build list
  2 = jobs, details and build details

  This template currently handles informations from the jobs list and the job details for the
  atom reader and build times for the chart widget.
-->

<xsl:template match="/">
  <atom:feed>
    <xsl:variable name="buildDataLimit" select="(date:seconds() - 86400 * 14) * 1000"/>
    <xsl:variable name="hasBuildData" select="count(*/job/build[timestamp &gt; $buildDataLimit]) &gt; 0"/>
    <xsl:if test="$hasBuildData">
      <csm:chart-options>
        <csm:axis-x mode="time"/>
        <csm:axis-y mode="milliseconds"/>
      </csm:chart-options>
    </xsl:if>
    <xsl:for-each select="*/job[lastBuild/result = 'FAILURE']">
      <xsl:sort select="lastBuild/timestamp" data-type="number" order="descending"/>
      <xsl:if test="position() &lt; $ENTRY_LIMIT">
        <xsl:call-template name="entry">
          <xsl:with-param name="job" select="."/>
          <xsl:with-param name="buildDataLimit" select="$buildDataLimit"/>
          <xsl:with-param name="hasBuildData" select="$hasBuildData"/>
        </xsl:call-template>
      </xsl:if>
    </xsl:for-each>
    <xsl:variable name="failures" select="count(*/job[lastBuild/result = 'FAILURE'])"/>
    <xsl:for-each select="*/job[lastBuild/result != 'FAILURE']">
      <xsl:sort select="lastBuild/timestamp" data-type="number" order="descending"/>
      <xsl:if test="position() &lt; ($ENTRY_LIMIT - $failures)">
        <xsl:call-template name="entry">
          <xsl:with-param name="job" select="."/>
          <xsl:with-param name="buildDataLimit" select="$buildDataLimit"/>
          <xsl:with-param name="hasBuildData" select="$hasBuildData"/>
        </xsl:call-template>
      </xsl:if>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<xsl:template name="entry">
  <xsl:param name="job"/>
  <xsl:param name="buildDataLimit" select="0"/>
  <xsl:param name="hasBuildData" select="false()"/>
  <xsl:variable name="lastestBuild" select="round($job/lastBuild/timestamp div 1000)"/>
  <atom:entry>
    <atom:title><xsl:value-of select="$job/name"/></atom:title>
    <atom:id><xsl:value-of select="$job/url"/></atom:id>
    <atom:updated>
      <xsl:choose>
        <xsl:when test="$lastestBuild > 0">
          <xsl:value-of select="date:add('1970-01-01T00:00:00Z', date:duration($lastestBuild))"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="date:date-time()"/>
        </xsl:otherwise>
      </xsl:choose>
    </atom:updated>
    <atom:link ref="alternate" type="text/html" href="{$job/url}"/>
    <xsl:if test="$job/healthReport">
      <atom:summary type="xhtml">
        <p>
          <xsl:for-each select="$job/healthReport">
            <xsl:if test="position() &gt; 1">
              <xsl:text>, </xsl:text><xhtml:br/>
            </xsl:if>
            <xsl:value-of select="description"/>
          </xsl:for-each>
        </p>
      </atom:summary>
    </xsl:if>
    <xsl:variable name="status">
      <xsl:choose>
        <xsl:when test="starts-with($job/color, 'red')">
          <xsl:text>error</xsl:text>
        </xsl:when>
        <xsl:when test="starts-with($job/color, 'yellow')">
          <xsl:text>warning</xsl:text>
        </xsl:when>
        <xsl:when test="starts-with($job/color, 'disabled')">
          <xsl:text>warning</xsl:text>
        </xsl:when>
        <xsl:otherwise>
          <xsl:text>information</xsl:text>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <csm:status><xsl:value-of select="$status"/></csm:status>
    <csm:icon>
      <xsl:attribute name="src">
        <xsl:value-of select="$FEED_PATH"/>
        <xsl:text>../</xsl:text>
        <xsl:choose>
          <xsl:when test="contains($job/color, '_anim')">
            <xsl:text>img/refresh.png</xsl:text>
          </xsl:when>
          <xsl:when test="$status = 'error'">
            <xsl:text>img/face-devilish.png</xsl:text>
          </xsl:when>
          <xsl:when test="$job/healthReport">
            <xsl:call-template name="icon-from-health-report">
              <xsl:with-param name="reports" select="$job/healthReport"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="starts-with($job/color, 'disabled')">
            <xsl:text>img/face-plain.png</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>img/face-angel.png</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>
      <xsl:if test="contains($job/color, '_anim')">
        <xsl:attribute name="animation">
          <xsl:text>rotate</xsl:text>
        </xsl:attribute>
      </xsl:if>
    </csm:icon>
    <xsl:if test="$hasBuildData">
      <csm:data-series>
        <xsl:for-each select="$job/build[timestamp &gt; $buildDataLimit]">
          <csm:data-point x="{timestamp}" y="{duration}"/>
        </xsl:for-each>
      </csm:data-series>
    </xsl:if>
  </atom:entry>
</xsl:template>

<xsl:template name="icon-from-health-report">
  <xsl:param name="reports"/>
  <xsl:variable name="health" select="func:min($reports/score)"/>
  <xsl:choose>
    <xsl:when test="$health &lt;= 20">img/weather-storm.png</xsl:when>
    <xsl:when test="$health &lt;= 40">img/weather-showers-scattered.png</xsl:when>
    <xsl:when test="$health &lt;= 60">img/weather-overcast.png</xsl:when>
    <xsl:when test="$health &lt;= 80">img/weather-few-clouds.png</xsl:when>
    <xsl:otherwise>img/weather-clear.png</xsl:otherwise>
  </xsl:choose>
</xsl:template>

<func:function name="func:min">
  <xsl:param name="scores"/>
  <xsl:variable name="result">
    <xsl:for-each select="$scores">
      <xsl:sort select="." data-type="number"/>
      <xsl:if test="position() = 1">
        <xsl:value-of select="."/>
      </xsl:if>
    </xsl:for-each>
  </xsl:variable>
  <func:result select="$result"/>
</func:function>

</xsl:stylesheet>