<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
>

<xsl:import href="rss/atom.xsl"/>

<xsl:template name="images">
  <xsl:variable name="fromBackgroundImage" select="substring-after(description, 'background-image:url(/')"/>
  <xsl:variable name="backgroundImageWithoutSize" select="substring-before($fromBackgroundImage, '?s=16')"/>
  <xsl:if test="$backgroundImageWithoutSize">
    <atom:link rel="image" href="{/rss/channel/link}{$backgroundImageWithoutSize}?s=48"/>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>