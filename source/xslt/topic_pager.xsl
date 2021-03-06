<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 	<xsl:output method="html"/>
		
    <xsl:template match="/posts/pager">
    	<xsl:call-template name="loop" />
    </xsl:template>

    <xsl:template name="loop">
    	<xsl:param name="total_pages" select="@total_pages" />
    	<xsl:param name="i" select="@start_pg" />
  			
 			<xsl:choose>
				<xsl:when test="$i = @current_pg">
 					<button id="curr_pg" type="button">
 						<xsl:value-of select="$i" />
 					</button>
 			
 				</xsl:when>
				<xsl:otherwise>
					<button class="paging_btns" type="button" onClick="AjaxTopic.paging_topic({@current_pg},{$i},'{@earliest_date}')">
						<xsl:value-of select="$i" />
					</button>	
				</xsl:otherwise>
			</xsl:choose>
 			
    		<xsl:if test="$i &lt; $total_pages">
    			<xsl:call-template name="loop">
    				<xsl:with-param name="i" select="$i + 1" />
    			</xsl:call-template>
    		</xsl:if>
    		
    </xsl:template>
    
</xsl:stylesheet>
