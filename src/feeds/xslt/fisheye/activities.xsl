<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
>

<xsl:import href="../rss/atom.xsl"/>

<xsl:param name="FEED_PATH"></xsl:param>

<xsl:template name="images">
  <xsl:variable name="fromBackgroundImage" select="substring-after(description, 'background-image:url(/')"/>
  <xsl:variable name="backgroundImageWithoutSize" select="substring-before($fromBackgroundImage, '?s=16')"/>
  <xsl:choose>
    <xsl:when test="$backgroundImageWithoutSize and not(contains($backgroundImageWithoutSize, 'avatar/j_doe'))">
      <atom:link rel="image" href="{/rss/channel/link}{$backgroundImageWithoutSize}?s=48"/>
    </xsl:when>
    <xsl:otherwise>
      <atom:link rel="image" href="{$FEED_PATH}../img/user-no-avatar.png"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>