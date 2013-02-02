<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:func="http://exslt.org/functions"
  xmlns:date="http://exslt.org/dates-and-times"
  extension-element-prefixes="func date"
>

<xsl:param name="FEED_PATH"></xsl:param>

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
    <xsl:for-each select="*/job">
      <atom:entry>
        <atom:title><xsl:value-of select="name"/></atom:title>
        <atom:id><xsl:value-of select="url"/></atom:id>
        <atom:updated><xsl:value-of select="date:date-time()"/></atom:updated>
        <atom:link ref="alternate" type="text/html" href="{url}"/>
        <xsl:if test="healthReport">
          <atom:summary type="xhtml">
            <p>
              <xsl:for-each select="healthReport">
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
            <xsl:when test="starts-with(color, 'red')">
              <xsl:text>error</xsl:text>
            </xsl:when>
            <xsl:when test="starts-with(color, 'yellow')">
              <xsl:text>warning</xsl:text>
            </xsl:when>
            <xsl:when test="starts-with(color, 'disabled')">
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
              <xsl:when test="contains(color, '_anim')">
                <xsl:text>img/refresh-animated.png</xsl:text>
              </xsl:when>
              <xsl:when test="$status = 'error'">
                <xsl:text>img/face-devilish.png</xsl:text>
              </xsl:when>
              <xsl:when test="healthReport">
                <xsl:call-template name="icon-from-health-report">
                  <xsl:with-param name="reports" select="healthReport"/>
                </xsl:call-template>
              </xsl:when>
              <xsl:when test="starts-with(color, 'disabled')">
                <xsl:text>img/face-plain.png</xsl:text>
              </xsl:when>
              <xsl:otherwise>
                <xsl:text>img/face-angel.png</xsl:text>
              </xsl:otherwise>
            </xsl:choose>
          </xsl:attribute>
        </csm:icon>
        <xsl:if test="$hasBuildData">
          <csm:data-series>
            <xsl:for-each select="build[timestamp &gt; $buildDataLimit]">
              <csm:data-point x="{timestamp}" y="{duration}"/>
            </xsl:for-each>
          </csm:data-series>
        </xsl:if>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
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