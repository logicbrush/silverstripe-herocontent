<% if $ParentID %>
	<nav aria-label="Breadcrumb">
		<ol itemscope itemtype="https://schema.org/BreadcrumbList">
			<% loop $getBreadcrumbItems(0, false, true) %>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<% if not $IsFirst %><span aria-hidden="true">/</span><% end_if %>
					<% if $IsLast %>
						<span itemprop="name" aria-current="page">$MenuTitle.XML</span>
					<% else %>
						<a itemprop="item" href="$Link.ATT"><span itemprop="name">$MenuTitle.XML</span></a>
					<% end_if %>
					<meta itemprop="position" content="$Pos">
				</li>
			<% end_loop %>
		</ol>
	</nav>
<% end_if %>
