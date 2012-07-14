<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:xCal="urn:ietf:params:xml:ns:xcal"
  xmlns:date="http://exslt.org/dates-and-times"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="date func"
>

<xsl:strip-space elements="*"/>

<xsl:template match="/*">
  <xsl:variable name="currentDate" select="date:date-time()"/>
  <atom:feed>
    <atom:id>urn:ohloh/codehistory</atom:id>
    <atom:updated><xsl:value-of select="$currentDate"/></atom:updated>
    <xsl:for-each select="//series/series">
      <atom:entry>
        <atom:title><xsl:value-of select="name"/></atom:title>
        <atom:id>urn:ohloh/codehistory/<xsl:value-of select="name"/></atom:id>
        <atom:updated><xsl:value-of select="$currentDate"/></atom:updated>
        <xsl:variable name="last" select="data/data[position() = last()]"/>
        <atom:summary>
          Lines: <xsl:value-of select="$last/*[position() = 2]"/>
        </atom:summary>
        <csm:data-series>
          <xsl:for-each select="data/data">
            <csm:data-point>
              <csm:data-point-x><xsl:value-of select="data[position() = 1]"/></csm:data-point-x>
              <csm:data-point-y><xsl:value-of select="data[position() = 2]"/></csm:data-point-y>
            </csm:data-point>
          </xsl:for-each>
        </csm:data-series>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>


</xsl:stylesheet>