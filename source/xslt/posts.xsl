<?xml version="1.0"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" indent="yes" encoding="UTF-8" />

	<xsl:template match="/">

		<xsl:for-each select="/posts/post">
			<div id="{concat('topic_',@id)}" class="topic">
				<span class="title"><a href="{concat('reply/',@id)}/"><xsl:value-of select="@title" /></a></span>
				<span class="date"><xsl:value-of select="@date" /></span>
				<span class="author_name"><xsl:value-of select="@author" /></span>
			</div>
		</xsl:for-each>
		
		<xsl:for-each select="/posts/topic">
			<div id="{concat('topic_',@id)}" class="topic"
				onmouseover="Reply.show_button('{concat('topic_',@id)}')"
				onmouseout="Reply.hide_button('{concat('topic_',@id)}')">
				<span class="title">
					<xsl:value-of select="@title" />
				</span>				
				<span class="date">
					<xsl:value-of select="@date" />
				</span>
				<span class="author_name">
					<xsl:value-of select="@author" />
				</span>
				<span class="message">
					<xsl:value-of select="@message" disable-output-escaping="yes" />
					<xsl:if test="/posts/@pg_num = 1">
						<button onclick="Reply.pre_reply('{concat('topic_',@id)}');"
							type="button" class="reply_btn">reply</button>
					</xsl:if>
				</span>
			</div>
		</xsl:for-each>

		<xsl:for-each select="/posts/reply">

			<xsl:choose>
				<xsl:when test="@type = 'reply_to_topic'">
				
					<div id="{concat('reply_',@id)}" class="reply_to_topic"
						onmouseover="Reply.show_button('{concat('reply_',@id)}')"
						onmouseout="Reply.hide_button('{concat('reply_',@id)}')">
						<span class="reply_to_topic_tri"></span>
						<span class="top_tri"></span>						
						<span class="date">
							<xsl:value-of select="@date" />
						</span>
						<span class="author_name">
							<xsl:value-of select="@author" />
						</span>
						<span class="message">
							<xsl:value-of select="@message" disable-output-escaping="yes" />
							<button style="display: none;" type="button" class="reply_btn"
								onclick="Reply.pre_reply('{concat('reply_',@id)}');">reply</button>
						</span>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<div id="{concat('reply_',@id)}" class="reply_to_reply"
						onmouseover="Reply.show_button('{concat('reply_',@id)}')"
						onmouseout="Reply.hide_button('{concat('reply_',@id)}')">
						<span class="reply_to_reply_tri"></span>
						<span class="top_tri"></span>
						<xsl:if test="@type = 'reply_to_reply'">
							<span class="reply_to_author">
								@<xsl:value-of select="@reply_to" />
							</span>
						</xsl:if>
						<span class="date">
							<xsl:value-of select="@date" />
						</span>
						<span class="author_name">
							<xsl:value-of select="@author" />
						</span>
						<span class="message">
							<xsl:value-of select="@message" disable-output-escaping="yes" />
							<button style="display: none;" type="button" class="reply_btn"
								onclick="Reply.pre_reply('{concat('reply_',@id)}');">reply</button>
						</span>
					</div>

				</xsl:otherwise>
			</xsl:choose>

		</xsl:for-each>

	</xsl:template>

</xsl:stylesheet>
