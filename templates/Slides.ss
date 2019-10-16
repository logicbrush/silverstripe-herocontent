<% require css('logicbrush/silverstripe-herocontent:thirdparty/slick.css') %>
<% require javascript('logicbrush/silverstripe-herocontent:thirdparty/slick.min.js') %>

<div id="rotate-slide-{$Slides.First.ID}" class="rotate">
	<% loop $Slides %>
	<div class="slide" style="
		position: relative;
		<% if $Image %>
		background: url({$Image.URL}) center center; 
		background-size: cover;
		min-height: {$Image.Height}px
		<% end_if %>
		">
		<% if $Content %>
		<div class="grid-aligned" style="position: absolute; width: 100%">
			<div class="span-all typography">
				$Content
			</div>
		</div>
		<% end_if %>
		<% if $AdditionalHTML %>
		$AdditionalHTML
		<% end_if %>
	</div>
	<% end_loop %>
</div>

<% if $Slides.Count > 1 %>
<script type="text/javascript" defer>
	jQuery('#rotate-slide-{$Slides.First.ID}').slick({
    infinite: true,
    fade: true,
    autoplay: true,
    autoplaySpeed: 8000,
    speed: 1000,
    prevArrow: '<div class="slick-prev"><span class="fas fa-chevron-left" aria-hidden="true"></span></div>',
    nextArrow: '<div class="slick-next"><span class="fas fa-chevron-right" aria-hidden="true"></span></div>',
	});
</script>
<% end_if %>
