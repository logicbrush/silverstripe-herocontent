<% require css('logicbrush/silverstripe-herocontent:thirdparty/slick.css') %>
<% require javascript('logicbrush/silverstripe-herocontent:thirdparty/slick.min.js') %>

<div id="rotate-slide-{$Slides.First.ID}" class="rotate">
		<% loop $Slides %>
		<img
				src="{$Image.FocusFill(3360,1890).URL}"
						 alt="{$Image.Title}"
		/>
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
