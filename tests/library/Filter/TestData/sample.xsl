<?xml version="1.0"?>
<xsl:stylesheet 
  version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
<xsl:param name="PARAMETER"></xsl:param>

<xsl:template match="/">
  <success>
    <xsl:if test="$PARAMETER != ''">
      <xsl:attribute name="with-parameter">yes</xsl:attribute>
    </xsl:if>
  </success>
</xsl:template>

</xsl:stylesheet>