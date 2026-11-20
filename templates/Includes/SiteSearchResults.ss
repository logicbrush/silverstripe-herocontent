<% if $Results %>
	<ul class="hc-site-search-results">
		<% loop $Results %>
			<li>
				<article>
					<a href="$Link.ATT">
						<% if $PageIconURL %>
							<img class="hc-site-search-result-icon" src="$PageIconURL.ATT" alt="" width="24" height="24">
						<% else_if $IconClass %>
							<span class="hc-site-search-result-icon $IconClass.ATT" aria-hidden="true"></span>
						<% end_if %>
						<div class="hc-site-search-result-text">
							<h2 class="hc-site-search-result-title">$Title.XML</h2>
							<% if $MetaDescription %>
								<p class="hc-site-search-result-description">$MetaDescription.XML</p>
							<% else_if $Content %>
								<p class="hc-site-search-result-description">$Content.Summary(15).XML</p>
							<% end_if %>
						</div>
						<span class="hc-site-search-result-caret" aria-hidden="true">
							<svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
								stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
								<path d="m6 3 5 5-5 5" />
							</svg>
						</span>
					</a>
				</article>
			</li>
		<% end_loop %>
	</ul>
<% else %>
	<p class="hc-site-search-no-results">
		<small><%t Logicbrush\HeroContent.NO_SEARCH_RESULTS 'No matching pages found.' %></small>
	</p>
<% end_if %>

<% if $ViewMoreLink %>
	<p class="hc-site-search-view-more">
		<a href="$ViewMoreLink.ATT"><%t Logicbrush\HeroContent.VIEW_ALL_SEARCH_RESULTS 'View all search results' %></a>
	</p>
<% end_if %>
