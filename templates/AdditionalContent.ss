<% if $AdditionalContent %>
	<% require css('logicbrush/silverstripe-herocontent:client/dist/additional-content.css') %>
	<% loop $AdditionalContent %>
		<% if $IsFirstBlockInStyle %>
			<% if not $IsFirst %></div></section><% end_if %>
			<section class="additional $Style">
				<div class="container grid additional-content-grid">
		<% end_if %>
			<div <% if $CssID %>id="$CssID"<% end_if %> class="additional<% if $Icon %> with-icon<% end_if %> typography<% if $AdditionalCSSClasses %> $AdditionalCSSClasses.ATT<% end_if %>"<% if $Columns && $Columns != 'all' %> style="--additional-content-columns: $Columns.ATT"<% end_if %>>
				<% if $Image %><img src="$Image.ScaleMaxWidth(600).URL" class="right width-one-half" srcset="$Image.ScaleMaxWidth(600).URL 1x, $Image.ScaleMaxWidth(1200).URL 1.3x" alt="$Image.Title.XML" /><% end_if %>
				<% if $Icon %>$IconHTML<% end_if %>
				$Content
				$HTML.RAW
			</div>
	<% end_loop %>
			</div>
		</section>
<% end_if %>
