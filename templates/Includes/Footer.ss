<footer id="page-footer">
	<div class="container">
		<nav aria-label="Footer navigation">
			<% loop $FooterMenuColumns %>
				<ul>
					<% loop $Pages %>
						<li>
							<a href="$Link.ATT" <% if $LinkingMode == 'current' %>aria-current="page"<% end_if %>>
								<strong>$MenuTitle.XML</strong>
							</a>
							<% if $FooterChildren %>
								<ul>
									<% loop $FooterChildren %>
										<% if $Parent.ClassName != 'SilverStripe\Blog\Model\Blog' || $PublishDate.Year == $Now.Year || $IsFirst %>
											<li>
												<a href="$Link.ATT" <% if $LinkingMode == 'current' %>aria-current="page"<% end_if %>>$MenuTitle.XML</a>
											</li>
										<% end_if %>
									<% end_loop %>
								</ul>
							<% end_if %>
						</li>
					<% end_loop %>
				</ul>
			<% end_loop %>
		</nav>

		<div class="footer-contact" itemscope itemtype="https://schema.org/Organization"<% if $SiteConfig.Logo %> itemref="site-organization-logo"<% end_if %>>
			<address>
				<p>
					<a itemprop="url" href="$BaseHref.ATT">
						<strong itemprop="name"><% if $SiteConfig.ContactName %>$SiteConfig.ContactName.XML<% else %>$SiteConfig.Title.XML<% end_if %></strong>
					</a>
				</p>
				<% if $SiteConfig.OfficeAddr %>
					<p itemprop="address">$SiteConfig.OfficeAddr.XML</p>
				<% end_if %>
				<% if $SiteConfig.Email %>
					<p><a itemprop="email" href="mailto:{$SiteConfig.Email.ATT}">$SiteConfig.Email.XML</a></p>
				<% end_if %>
				<% if $SiteConfig.OfficePhone %>
					<p><a itemprop="telephone" href="tel:{$SiteConfig.OfficePhone.ATT}">$SiteConfig.OfficePhone.XML</a></p>
				<% end_if %>
				<% if $SiteConfig.OfficeFax %>
					<p>Fax: <span itemprop="faxNumber">$SiteConfig.OfficeFax.XML</span></p>
				<% end_if %>
				<% if $SiteConfig.MobilePhone %>
					<p><a href="tel:{$SiteConfig.MobilePhone.ATT}">$SiteConfig.MobilePhone.XML</a></p>
				<% end_if %>
			</address>

			<% if $SiteConfig.LinkedInURL || $SiteConfig.FacebookURL || $SiteConfig.GitHubURL || $SiteConfig.TwitterURL || $SiteConfig.YouTubeURL || $SiteConfig.InstagramURL || $SiteConfig.PinterestURL %>
				<nav aria-label="Social media">
					<ul>
						<% if $SiteConfig.LinkedInURL %><li><a itemprop="sameAs" href="$SiteConfig.LinkedInURL.ATT">LinkedIn</a></li><% end_if %>
						<% if $SiteConfig.FacebookURL %><li><a itemprop="sameAs" href="$SiteConfig.FacebookURL.ATT">Facebook</a></li><% end_if %>
						<% if $SiteConfig.GitHubURL %><li><a itemprop="sameAs" href="$SiteConfig.GitHubURL.ATT">GitHub</a></li><% end_if %>
						<% if $SiteConfig.TwitterURL %><li><a itemprop="sameAs" href="$SiteConfig.TwitterURL.ATT">Twitter / X</a></li><% end_if %>
						<% if $SiteConfig.YouTubeURL %><li><a itemprop="sameAs" href="$SiteConfig.YouTubeURL.ATT">YouTube</a></li><% end_if %>
						<% if $SiteConfig.InstagramURL %><li><a itemprop="sameAs" href="$SiteConfig.InstagramURL.ATT">Instagram</a></li><% end_if %>
						<% if $SiteConfig.PinterestURL %><li><a itemprop="sameAs" href="$SiteConfig.PinterestURL.ATT">Pinterest</a></li><% end_if %>
					</ul>
				</nav>
			<% end_if %>
		</div>

		<p><small>&copy; $Now.Year $SiteConfig.Title.XML<br>All rights reserved.</small></p>
	</div>
</footer>
