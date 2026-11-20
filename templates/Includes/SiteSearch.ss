<% require javascript('logicbrush/silverstripe-herocontent:client/dist/site-search.js') %>

<details class="hc-site-search $SearchClass.ATT" data-site-search>
	<summary>
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
			stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
			aria-hidden="true" focusable="false">
			<circle cx="10.8" cy="10.8" r="6.8" />
			<path d="m16 16 5 5" />
		</svg>
		<span><%t Logicbrush\HeroContent.SEARCH 'Search' %></span>
	</summary>
	<div class="hc-site-search-panel">
		<form action="$SearchAction.ATT" method="get" role="search" data-site-search-form>
			<% include SiteSearchFields %>
			<p id="$SearchHelpID.ATT" class="hc-site-search-help">
				<%t Logicbrush\HeroContent.SEARCH_HELP 'Type at least 3 characters for suggestions.' %>
			</p>
		</form>
		<div id="$SearchSuggestionsID.ATT" class="hc-site-search-suggestions"
			role="region" aria-label="<%t Logicbrush\HeroContent.SEARCH_SUGGESTIONS 'Search suggestions' %>"
			aria-live="polite" data-site-search-results></div>
	</div>
</details>
