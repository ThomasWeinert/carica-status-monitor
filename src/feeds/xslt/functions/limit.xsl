<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="func"
>

<func:function name="func:limit">
  <xsl:param name="items" select="*"/>
  <xsl:param name="limit" select="0"/>
  <xsl:param name="offset" select="0"/>
  <func:result select="$items[$limit &lt;= 0 or (position() &gt; $offset and position() &lt;= $offset + $limit)]"/>
</func:function>

</xsl:stylesheet>