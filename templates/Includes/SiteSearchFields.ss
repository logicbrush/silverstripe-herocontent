<label for="$SearchInputID.ATT">
	<% if $SearchSiteName %>
		<%t Logicbrush\HeroContent.SEARCH_SITE_NAME 'Search {site}.' site=$SearchSiteName.XML %>
	<% else %>
		<%t Logicbrush\HeroContent.SEARCH_THIS_SITE 'Search this site' %>
	<% end_if %>
</label>
<div class="hc-site-search-controls">
	<input id="$SearchInputID.ATT" type="search" name="Search"
		value="$SearchValue.ATT" autocomplete="off" enterkeyhint="search"
		<% if $SearchAutocomplete %>
			aria-controls="$SearchSuggestionsID.ATT"
			aria-describedby="$SearchHelpID.ATT"
			data-site-search-input
		<% else %>
			autofocus
		<% end_if %>>
	<% if $SearchAutocomplete %>
		<button type="submit"><%t Logicbrush\HeroContent.SEARCH 'Search' %></button>
	<% end_if %>
</div>