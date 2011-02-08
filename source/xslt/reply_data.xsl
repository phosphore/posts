<?xml version="1.0"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" indent="yes" encoding="UTF-8" />

	<xsl:template match="/posts/data">	
   		<div id='current_pg' title="{@current_page}"></div>
    	<div id='earliest_date' title='{@earliest_date}'></div>
    	<div id='topic_id' title="{@topic_id}"></div>
	</xsl:template>

</xsl:stylesheet>
