<!DOCTYPE html>
<html lang="$ContentLocale.ATT">
	<head>
		<% base_tag %>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><% if $MetaTitle %>$MetaTitle.XML<% else %>$Title.XML<% end_if %> | $SiteConfig.Title.XML</title>
		$MetaTags
	</head>
	<body id="page-$ID" class="$ClassName<% if $AdditionalCSSClasses %> $AdditionalCSSClasses.ATT<% end_if %>">
		<a class="skip-link" href="#page-main">Skip to main content</a>

		<% include Header %>

		<main id="page-main">
			<% if $SearchResultsPage %>
				<section class="search-results">
					$SearchPageContent
				</section>
			<% else %>
				<% include Breadcrumbs %>

				<% if $HeroImage %>
					<img class="hero-image"
						src="$HeroImage.ScaleWidth($HeroImageWidth).URL.ATT"
						width="$HeroImage.ScaleWidth($HeroImageWidth).Width"
						height="$HeroImage.ScaleWidth($HeroImageWidth).Height"
						alt="" decoding="async">
				<% end_if %>

				<% if $HeroContent %>
					<section class="hero">
						$HeroContent
					</section>
				<% end_if %>

				<section class="page-content">
					<h1>$Title.XML</h1>
					$Content
					$Form
				</section>

				$FeaturedContent
			<% end_if %>
		</main>

		<% include Footer %>
		<% include MobileNavigation %>
	</body>
</html>
