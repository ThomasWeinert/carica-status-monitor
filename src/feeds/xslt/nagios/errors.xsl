<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:func="http://exslt.org/functions"
  xmlns:date="http://exslt.org/dates-and-times"
  extension-element-prefixes="func date">

<xsl:template match="/json">
  <atom:feed>
    <xsl:variable 
      name="hasErrors" 
      select="count(content//current_state[number(text()) &gt; 0]) &gt; 0"/>
    <xsl:choose>
      <xsl:when test="$hasErrors">
        <xsl:for-each select="content/*[.//current_state &gt; 0]">
          <xsl:variable name="serverCaption" select="@name"/>
          <xsl:call-template name="feed-entry">
            <xsl:with-param name="caption" select="$serverCaption"/>
            <xsl:with-param name="element" select="."/>
          </xsl:call-template>
          <xsl:for-each select="services/*[current_state &gt; 0]">
            <xsl:call-template name="feed-entry">
              <xsl:with-param name="caption">
                <xsl:value-of select="$serverCaption"/> - <xsl:value-of select="@name"/>
              </xsl:with-param>
              <xsl:with-param name="element" select="."/>
            </xsl:call-template>
          </xsl:for-each>
        </xsl:for-each>
      </xsl:when>
    </xsl:choose> 
  </atom:feed>
</xsl:template>  

<xsl:template name="feed-entry">
  <xsl:param name="caption"/>
  <xsl:param name="element"/>
  <xsl:if test="$element/current_state &gt; 0">
    <atom:entry>
      <atom:title><xsl:value-of select="$caption"/></atom:title>
      <atom:id><xsl:value-of select="$caption"/></atom:id>
      <atom:updated><xsl:value-of select="date:timestamp($element/last_state_change)"/></atom:updated>
      <atom:summary><xsl:value-of select="$element/plugin_output"/></atom:summary>
      <xsl:choose>
        <xsl:when test="$element/current_state &gt; 1">
          <csm:status>error</csm:status>
        </xsl:when>
        <xsl:when test="$element/current_state &gt; 0">
          <csm:status>warning</csm:status>
        </xsl:when>
      </xsl:choose>
    </atom:entry>
  </xsl:if>
</xsl:template>

<func:function name="date:timestamp">
  <xsl:param name="timestamp"/>
  <func:result select="date:add('1970-01-01T00:00:00Z', date:duration($timestamp))"/>
</func:function>
  
</xsl:stylesheet>