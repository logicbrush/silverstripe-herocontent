<% if $Menu(1) %>
	<button type="button" class="hc-mobile-navigation-toggle hc-mobile-navigation-button" hidden
		data-a11y-dialog-show="mobile-navigation"
		aria-controls="mobile-navigation" aria-expanded="false"
		aria-haspopup="dialog"
		aria-label="<%t Logicbrush\HeroContent.OPEN_MENU 'Open menu' %>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
			stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false">
			<path d="M3 6h18M3 12h18M3 18h18" />
		</svg>
		<span class="hc-mobile-navigation-label"><%t Logicbrush\HeroContent.MENU 'Menu' %></span>
	</button>
<% end_if %>
