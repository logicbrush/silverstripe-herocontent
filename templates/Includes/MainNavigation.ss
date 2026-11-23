<ul<% if $MobileNavigationDesktop %> data-mobile-navigation-desktop<% end_if %>>
	<% loop $Menu(1) %>
		<li>
			<a href="$Link.ATT" class="$LinkingMode<% if $MenuLinkCallToAction %> button<% end_if %>"
				<% if $LinkingMode == 'current' %>aria-current="page"<% end_if %>>
				<% if $MenuIcon %><span class="navigation-icon $MenuIcon.ATT" aria-hidden="true"></span><% end_if %>
				$MenuTitle.XML
			</a>
		</li>
	<% end_loop %>
</ul>
