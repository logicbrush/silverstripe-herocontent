<% if $FeaturedContent %>
	<% loop $FeaturedContent %>
		<% if $IsFirstBlockInStyle %>
			<% if not $IsFirst %></div></section><% end_if %>
			<section class="featured $Style">
				<div class="container grid" data-columns="12">
		<% end_if %>
			<div <% if $CssID %>id="$CssID"<% end_if %> class="featured<% if $AdditionalCSSClasses %> $AdditionalCSSClasses.ATT<% end_if %>"<% if $Columns %> data-columns="$Columns.ATT"<% end_if %>>
				<% if $Image %><img src="$Image.ScaleMaxWidth($ImageWidth).URL" class="right" srcset="$Image.ScaleMaxWidth($ImageWidth).URL 1x, $Image.ScaleMaxWidth($ImageSrcSetWidth).URL 1.3x" alt="$Image.Title.XML" /><% end_if %>
				<% if $Icon %>$IconHTML<% end_if %>
				$Content
				$HTML.RAW
			</div>
	<% end_loop %>
			</div>
		</section>
<% end_if %>
