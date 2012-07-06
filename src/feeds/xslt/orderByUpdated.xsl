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
  extension-element-prefixes="date func"
>

<xsl:strip-space elements="*"/>

<xsl:key name="entries-by-id" match="atom:entry" use="atom:id" />

<xsl:template match="/*">
  <xsl:copy>
    <xsl:copy-of select="@*"/>
    <xsl:copy-of select="*[name() != 'atom:entry']"/>
    <xsl:for-each select="atom:entry[count(. | key('entries-by-id', atom:id)[1]) = 1]">
      <xsl:sort select="atom:updated" order="descending"/>
      <xsl:copy>
        <xsl:copy-of select="node()|@*"/>
      </xsl:copy>
    </xsl:for-each>
  </xsl:copy>
</xsl:template>

</xsl:stylesheet>