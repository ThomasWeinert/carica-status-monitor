<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="func"
>

<!--  

  This template function looks for a token inside an string of tokens separated by whitespaces.
  It works like the token match operator in CSS (~=).

  Usage:
  
    <xsl:if test="func:contains-token(@class, 'sample')">...</xsl:if>

 -->
<func:function name="func:contains-token">
  <xsl:param name="haystack"></xsl:param>
  <xsl:param name="needle"></xsl:param>
  <xsl:variable name="normalizedHaystack" select="concat(' ', normalize-space($haystack), ' ')"/>
  <xsl:variable name="normalizedNeedle" select="concat(' ', normalize-space($needle), ' ')"/>
  <func:result select="$needle != '' and contains($haystack, $needle)"/>
</func:function>

</xsl:stylesheet>