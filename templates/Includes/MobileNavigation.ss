<% if $Menu(1) %>
	<% require css('logicbrush/silverstripe-herocontent:client/dist/mobile-navigation.css') %>
	<% require javascript('logicbrush/silverstripe-herocontent:client/dist/thirdparty/a11y-dialog.min.js') %>
	<% require javascript('logicbrush/silverstripe-herocontent:client/dist/mobile-navigation.js') %>

	<div id="mobile-navigation" class="hc-mobile-navigation"
		data-mobile-navigation aria-hidden="true"
		aria-labelledby="mobile-navigation-title">
		<div class="hc-mobile-navigation-overlay" data-a11y-dialog-hide></div>
		<div class="hc-mobile-navigation-panel" role="document">
			<button type="button" class="hc-mobile-navigation-button" data-a11y-dialog-hide
				aria-label="<%t Logicbrush\HeroContent.CLOSE_MENU 'Close menu' %>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
					stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false">
					<path d="m6 6 12 12M6 18 18 6" />
				</svg>
				<span class="hc-mobile-navigation-label"><%t Logicbrush\HeroContent.CLOSE 'Close' %></span>
			</button>
			<h2 id="mobile-navigation-title"><%t Logicbrush\HeroContent.MENU 'Menu' %></h2>
			$SiteSearch('mobile')
			<nav aria-labelledby="mobile-navigation-title">
				<% include MobileNavigationItems Items=$Menu(1) %>
			</nav>
		</div>
	</div>
<% end_if %>
