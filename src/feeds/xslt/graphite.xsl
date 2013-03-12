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

<xsl:template match="/">
  <atom:feed>
    <csm:chart-options>
      <csm:axis-x mode="time"/>
    </csm:chart-options>
    <xsl:for-each select="*/json">
      <atom:entry>
        <atom:title><xsl:value-of select="target"/></atom:title>
        <atom:id><xsl:value-of select="target"/></atom:id>
        <atom:updated><xsl:value-of select="date:date-time()"/></atom:updated>
        <csm:data-series>
          <xsl:for-each select="datapoints/datapoints">
            <csm:data-point x="{datapoints[2]}000" y="{datapoints[1]}"/>
          </xsl:for-each>
        </csm:data-series>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

</xsl:stylesheet>