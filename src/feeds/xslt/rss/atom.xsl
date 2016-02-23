<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:media="http://search.yahoo.com/mrss/"
  xmlns:google="http://base.google.com/ns/1.0"
  xmlns:openSearch="http://a9.com/-/spec/opensearch/1.1/"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  xmlns:rss="http://purl.org/rss/1.0/"
  extension-element-prefixes="date func"
>

<xsl:import href="dates.xsl"/>

<xsl:template match="/">
  <xsl:variable name="channel" select=".//channel|.//rss:channel"/>
  <atom:feed>
    <xsl:if test="$channel/language|$channel/dc:language">
      <xsl:attribute name="xml:lang">
        <xsl:value-of select="$channel/language|$channel/dc:language"/>
      </xsl:attribute>
    </xsl:if>
    <atom:title><xsl:value-of select="$channel/title|$channel/rss:title"/></atom:title>
    <atom:subtitle><xsl:value-of select="$channel/description|$channel/rss:description"/></atom:subtitle>
    <atom:link rel="self" href="{$channel/link|$channel/rss:link}"/>
    <atom:id><xsl:value-of select="$channel/link|$channel/rss:link"/></atom:id>
    <xsl:if test="$channel/pubDate">
      <atom:updated><xsl:value-of select="date:convertDateRssToAtom($channel/pubDate)"/></atom:updated>
    </xsl:if>
    <xsl:call-template name="author">
      <xsl:with-param name="rssAuthor" select="$channel/managingEditor"/>
      <xsl:with-param name="dcAuthor" select="$channel/dc:creator"/>
    </xsl:call-template>
    <xsl:if test="$channel/copyright|$channel/dc:rights">
      <atom:rights>
        <xsl:choose>
          <xsl:when test="$channel/copyright"><xsl:value-of select="$channel/copyright"/></xsl:when>
          <xsl:when test="$channel/dc:rights"><xsl:value-of select="$channel/dc:rights"/></xsl:when>
        </xsl:choose></atom:rights>
    </xsl:if>
    <xsl:call-template name="categories">
      <xsl:with-param name="categories" select="$channel/category"/>
    </xsl:call-template>
    <xsl:for-each select="$channel//item|.//rss:item">
      <atom:entry>
        <atom:title><xsl:value-of select="title|rss:title"/></atom:title>
        <atom:link rel="alternate" type="text/html" href="{link|rss:link}"/>
        <atom:id>
          <xsl:choose>
            <xsl:when test="guid"><xsl:value-of select="guid"/></xsl:when>
            <xsl:when test="link"><xsl:value-of select="link"/></xsl:when>
            <xsl:when test="rss:link"><xsl:value-of select="rss:link"/></xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="concat($channel/link|$channel/rss:link, '#', position())"/>
            </xsl:otherwise>
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
            <xsl:when test="dc:date">
              <xsl:value-of select="dc:date"/>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="date:date-time()"/></xsl:otherwise>
          </xsl:choose>
        </atom:updated>
        <xsl:call-template name="author">
          <xsl:with-param name="rssAuthor" select="author"/>
        </xsl:call-template>
        <xsl:call-template name="categories">
          <xsl:with-param name="categories" select="category"/>
        </xsl:call-template>
        <atom:content type="html"><xsl:value-of select="description|rss:description"/></atom:content>
        <xsl:call-template name="images"/>
        <xsl:copy-of select="media:*"/>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

<xsl:template name="author">
  <xsl:param name="rssAuthor"/>
  <xsl:param name="dcAuthor"/>
  <xsl:if test="$rssAuthor or $dcAuthor">
    <atom:author>
      <atom:name>
        <xsl:choose>
          <xsl:when test="$dcAuthor">
            <xsl:value-of select="$dcAuthor"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="$rssAuthor"/>
          </xsl:otherwise>  
        </xsl:choose>
      </atom:name>
      <xsl:if test="$rssAuthor and contains($rssAuthor, '@')">
        <atom:email><xsl:value-of select="$rssAuthor"/></atom:email>
      </xsl:if>
    </atom:author>
  </xsl:if>
</xsl:template>

<xsl:template name="categories">
  <xsl:param name="categories"/>
  <xsl:if test="$categories">
    <xsl:for-each select="$categories">
      <atom:category term="{text()}"/>
    </xsl:for-each>
  </xsl:if>
</xsl:template>

<xsl:template name="images">
  <xsl:for-each select="media:content[starts-with(@type, 'image/')]">
    <atom:link rel="image" type="{@type}" href="{@url}"/>
  </xsl:for-each>
</xsl:template>

</xsl:stylesheet>
