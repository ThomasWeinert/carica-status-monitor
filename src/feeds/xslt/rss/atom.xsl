<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="date func"
>

<xsl:import href="dates.xsl"/>

<xsl:template match="/">
  <xsl:variable name="channel" select="./rss/channel"/>
  <atom:feed>
    <xsl:if test="$channel/language">
      <xsl:attribute name="xml:lang">
        <xsl:value-of select="$channel/language"/>
      </xsl:attribute>
    </xsl:if>
    <atom:title><xsl:value-of select="$channel/title"/></atom:title>
    <atom:link href="{$channel/link}"/>
    <atom:id><xsl:value-of select="$channel/link"/></atom:id>
    <xsl:if test="$channel/pubDate">
      <atom:updated><xsl:value-of select="date:convertDateRssToAtom($channel/pubDate)"/></atom:updated>
    </xsl:if>
    <xsl:call-template name="author">
      <xsl:with-param name="rssAuthor" select="$channel/managingEditor"/>
    </xsl:call-template>
    <xsl:for-each select="$channel/item">
      <atom:entry>
        <atom:title><xsl:value-of select="title"/></atom:title>
        <atom:link href="{link}"/>
        <atom:id>
          <xsl:choose>
            <xsl:when test="guid"><xsl:value-of select="guid"/></xsl:when>
            <xsl:when test="link"><xsl:value-of select="link"/></xsl:when>
            <xsl:otherwise><xsl:value-of select="concat($channel/link, '#', posiiton())"/></xsl:otherwise>
          </xsl:choose>
        </atom:id>
        <atom:updated>
          <xsl:choose>
            <xsl:when test="pubDate">
              <xsl:value-of select="date:convertDateRssToAtom(pubDate)"/>
            </xsl:when>
            <xsl:when test="$channel/pubDate">
              <xsl:value-of select="date:convertDateRssToAtom($channel/pubDate)"/>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="date:date-time()"/></xsl:otherwise>
          </xsl:choose>
        </atom:updated>
        <xsl:call-template name="author">
          <xsl:with-param name="rssAuthor" select="author"/>
        </xsl:call-template>
        <atom:content><xsl:value-of select="description"/></atom:content>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<xsl:template name="author">
  <xsl:param name="rssAuthor"/>
  <xsl:if test="$rssAuthor">
    <atom:author>
      <atom:name><xsl:value-of select="$rssAuthor"/></atom:name>
      <xsl:if test="contains($rssAuthor, '@')">
        <atom:email><xsl:value-of select="$rssAuthor"/></atom:email>
      </xsl:if>
    </atom:author>
  </xsl:if>
</xsl:template>


</xsl:stylesheet>