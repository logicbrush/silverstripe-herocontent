<ul>
	<% loop $Items %>
		<li>
			<a href="$Link.ATT" class="$LinkingMode<% if $MenuLinkCallToAction %> button<% end_if %>"
				<% if $LinkingMode == 'current' %>aria-current="page"<% end_if %>>
				<% if $MenuIcon %><span class="navigation-icon $MenuIcon.ATT" aria-hidden="true"></span><% end_if %>
				$MenuTitle.XML
			</a>
			<% if $Children %>
				<% include MobileNavigationItems Items=$Children %>
			<% end_if %>
		</li>
	<% end_loop %>
</ul>
