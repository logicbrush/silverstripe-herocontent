<ul>
	<% loop $Items %>
		<li>
			<a href="$Link" class="$LinkingMode"
				<% if $LinkingMode == 'current' %>aria-current="page"<% end_if %>>
				$MenuTitle.XML
			</a>
			<% if $Children %>
				<% include MobileNavigationItems Items=$Children %>
			<% end_if %>
		</li>
	<% end_loop %>
</ul>
