<header id="page-header">
	<a id="site-organization-logo" class="site-brand" href="$BaseHref.ATT">
		<% if $SiteConfig.Logo %>
			<img src="$SiteConfig.Logo.ScaleWidth($SiteConfig.LogoWidth).URL.ATT"
				width="$SiteConfig.Logo.ScaleWidth($SiteConfig.LogoWidth).Width"
				height="$SiteConfig.Logo.ScaleWidth($SiteConfig.LogoWidth).Height"
				alt="$SiteConfig.Title.ATT">
		<% else %>
			<strong>$SiteConfig.Title.XML</strong>
		<% end_if %>
	</a>
	<nav aria-label="Main navigation">
		<% include MainNavigation MobileNavigationDesktop=1 %>
	</nav>
	<div class="site-header-controls">
		$SiteSearch('header')
		<% include MobileNavigationToggle %>
	</div>
</header>
