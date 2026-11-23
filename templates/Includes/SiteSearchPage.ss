<div class="hc-site-search-page">
	<header class="hc-site-search-page-heading">
		<h1><%t Logicbrush\HeroContent.SEARCH_RESULTS 'Search results' %></h1>
		<% if $Query %>
			<p><%t Logicbrush\HeroContent.SEARCHED_FOR 'You searched for' %> <q>$Query.XML</q></p>
		<% end_if %>
	</header>
	<% if $Query %>
		<% include SiteSearchResults %>
		<% if $Results.MoreThanOnePage %>
			<nav class="hc-site-search-pagination" aria-label="<%t Logicbrush\HeroContent.SEARCH_RESULT_PAGES 'Search result pages' %>">
				<ul>
					<% if $Results.NotFirstPage %>
						<li><a href="$Results.PrevLink.ATT" rel="prev"><%t Logicbrush\HeroContent.PREVIOUS 'Previous' %></a></li>
					<% end_if %>
					<% loop $Results.PaginationSummary(4) %>
						<li>
							<% if $CurrentBool %>
								<span aria-current="page">$PageNum</span>
							<% else_if $Link %>
								<a href="$Link.ATT">$PageNum</a>
							<% else %>
								<span aria-hidden="true">&hellip;</span>
							<% end_if %>
						</li>
					<% end_loop %>
					<% if $Results.NotLastPage %>
						<li><a href="$Results.NextLink.ATT" rel="next"><%t Logicbrush\HeroContent.NEXT 'Next' %></a></li>
					<% end_if %>
				</ul>
			</nav>
		<% end_if %>
	<% else %>
		<p><%t Logicbrush\HeroContent.ENTER_SEARCH_TERM 'Enter a search term to find pages.' %></p>
	<% end_if %>
</div>
