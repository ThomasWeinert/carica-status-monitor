<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:csm="http://thomas.weinert.info/carica/ns/status-monitor"
  xmlns:date="http://exslt.org/dates-and-times"
  extension-element-prefixes="date"
>

<xsl:template match="/">
  <atom:feed>
    <xsl:for-each select="./listView/job">
      <atom:entry>
        <atom:title><xsl:value-of select="name"/></atom:title>
        <atom:id><xsl:value-of select="url"/></atom:id>
        <atom:updated><xsl:value-of select="date:date-time()"/></atom:updated>
        <xsl:variable name="status">
          <xsl:choose>
            <xsl:when test="starts-with(color, 'red')">
              <xsl:text>error</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>information</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:variable>
        <csm:status><xsl:value-of select="$status"/></csm:status>
        <csm:icon>
          <xsl:attribute name="src">
            <xsl:choose>
              <xsl:when test="contains(color, '_anim')">
                <xsl:text>img/refresh-animated.png</xsl:text>
              </xsl:when>
              <xsl:when test="$status = 'error'">
                <xsl:text>img/face-devilish.png</xsl:text>
              </xsl:when>
              <xsl:otherwise>
                <xsl:text>img/face-angel.png</xsl:text>
              </xsl:otherwise>
            </xsl:choose>
          </xsl:attribute>
        </csm:icon>
      </atom:entry>
    </xsl:for-each>
  </atom:feed>
</xsl:template>

</xsl:stylesheet>