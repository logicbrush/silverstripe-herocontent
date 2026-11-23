# silverstripe-herocontent

A SilverStripe CMS module for reusable page content, navigation, search, and
site settings. Alongside hero backgrounds and optional rotating slides, it
provides ordered featured content blocks, page-level presentation and
navigation settings, site search and mobile navigation, and versioned contact,
logo, and social-profile settings.

## Why?

The module brings commonly reused CMS features together while allowing each
site's theme to control its presentation, making it a useful foundation for a
small-business website. Use the features independently as needed: add hero
content and ordered page blocks, configure page navigation and CSS classes,
provide searchable pages and mobile navigation, and manage shared contact and
identity settings.

## Installation

Requires [PHP](https://www.php.net/manual/en/) `^8.1` and
[Silverstripe CMS 5](https://docs.silverstripe.org/en/5/).
Composer installs the module's other dependencies; see
[`composer.json`](composer.json).
Composer suggests [Pico CSS](https://picocss.com/) because the starter
templates use its classes. Pico is optional if your theme provides equivalent
styles.

```sh
composer require "logicbrush/silverstripe-herocontent"
```

## General Usage

Installation of this module will add `Hero content` and `Featured content`
tabs to all of your pages within the CMS. In `Hero content`, you can select a
background image and set up zero or more slides. Each slide can include an image,
formatted content, and extra HTML. The default `Slides.ss` template uses Slick
to fade between slides every eight seconds when there is more than one slide.
The module includes Slick, but the theme must provide jQuery for the slider to
initialize.

Add `$HeroImage` or `$HeroImage.URL` in your template file to reference the hero
background image. We frequently like to add it with inline CSS to our `body`
tag, usually with a transparent overlay and `background-size: cover` and
`background-attachment: fixed`.  That way it will appear behind all content on
the page and stick to the top of the browser window.  You could alternatively
apply it to an independent `div`.

The starter page templates scale the hero image to 2400 pixels by default.
Override `hero_image_width` in your project YAML config to change that width:

```yaml
Logicbrush\HeroContent\Model\WithHeroContentExtension:
  hero_image_width: 1800
```

Use `$HeroContent` in your template to render your slides.  If you wish, you can
copy the `Slides.ss` template file into your theme and modify to your liking.
You can place this either above or below your regular `$Content` as you like.
Or, you can leave it out entirely if you don't need rotating content.

In `Featured content`, you can create, edit, and order content blocks.
The module renders these by default with `FeaturedContent.ss`; copy that
template into your theme to customize the output. The model class is
`FeaturedContent`; existing `ContentBlock` and `AdditionalContent` class names
are remapped to it during `dev/build`. The `ContentBlock` database table and
its fields are preserved. Run `dev/build` after upgrading to apply the
remapping. Update project code that refers to the old `AdditionalContent` PHP
class.

Blocks can also have an image, an icon class, a Group Style, a CSS ID, and
Extra HTML. Group Style groups blocks into sections; the theme must provide
styles or an icon font for any icons used.

The `FeaturedContent.ss` image variant width defaults to 600 pixels in the
module's [`_config/herocontent.yml`](_config/herocontent.yml). Override
`image_width` in your project's YAML config to change it. The `srcset` variant
is twice this width:

```yaml
Logicbrush\HeroContent\Model\FeaturedContent:
  image_width: 800
```

Theme overrides can use `$ImageWidth` and `$ImageSrcSetWidth` for the same
configured values. This is configuration only and does not add a database
field.

The module's default `FeaturedContent.ss` declares a 12-column container with
`data-columns="12"` and exposes each block's `Columns` value in its own
`data-columns` attribute, without grid styles. Themes can use these attributes
to define their layout; the Bootstrap theme supplies a responsive 12-column
grid in its own template and Sass.

Each content block's **Extra** tab includes **Additional CSS classes**,
stored separately from its Group Style. Enter space-separated class names to
style a block independently without splitting its group or changing its column
span. The default template appends `$AdditionalCSSClasses.ATT` to the block's
wrapper; update theme overrides of `FeaturedContent.ss` in the same way.
The field follows the block's normal Draft/Live publishing workflow. Run
`dev/build?flush=1` after adding it. Class names and their styling are supplied
by the theme, not the module.

## Starter templates

The module includes a base `Page.ss` and starter `Breadcrumbs.ss`,
`Footer.ss`, `Header.ss`, and `MainNavigation.ss` includes. They provide
semantic markup for the module's page content, search, navigation, and site
settings. Project theme templates take precedence and can override these
defaults.

The starter templates use Pico CSS classes for basic layout but do not bundle
Pico CSS, Bootstrap styles, or icon fonts. Install the suggested Pico package,
or provide equivalent styles in your theme. The base page template uses the
page title as its `h1`; avoid adding another `h1` in hero content, or customize
`Page.ss` if the hero provides the page heading.

## Page CSS classes

Run `dev/build?flush=1` after adding this feature. Every page's **Settings**
form includes an **Additional CSS classes** field. Enter space-separated
class names, such as `service-page featured`. The field follows the page's
normal Draft/Live publishing workflow and is not inherited by child pages.

Append `$AdditionalCSSClasses.ATT` to your theme's body classes:

```ss
<body class="$ClassName $AdditionalCSSClasses.ATT">
```

Keep the attribute escaping; the module stores plain text and does not
change the theme's markup or supply styles for these classes.

## Page navigation settings

Every page's **Settings** form includes:

* **Menu icon** (`MenuIcon`, `Varchar(50)`) for icon CSS classes.
* **Display menu link as a call to action** (`MenuLinkCallToAction`, `Boolean`).
* **Hide in footer** (`HideInFooter`, `Boolean`).
* **Show subpages in footer** (`DisplayChildrenInFooter`, `Boolean`).

`WithPageNavigationExtension` adds these fields to `Page`, retaining their
existing column names, types, and stored values. They follow the page's normal
Draft/Live workflow. Run `dev/build?flush=1` after adding this feature to a
project; moving identical declarations from application code does not move or
rename the existing columns.

Remove application-local declarations and duplicate Settings editors for these
fields when adopting the extension. The starter `MainNavigation.ss` renders
menu icons and call-to-action classes; include it with
`MobileNavigationDesktop=1` to connect it to the mobile menu. The starter
`Footer.ss` displays visible top-level pages that are in menus or search and
not hidden in the footer. It includes eligible subpages when their parent has
**Show subpages in footer** enabled; for blog pages, it shows up to three recent
posts. It also renders configured contact details and social profile links.
The templates provide markup only; icon fonts, visual styles, and
project-specific navigation remain the theme's responsibility.

## Site search

The module adds a `Search` action to `PageController`, available at each page's
`Search` URL. Add `$SiteSearch('header')` to the theme's header for desktop
autocomplete. The mobile navigation includes a separate search form that
submits directly to results without autocomplete. Search results are limited to
Live pages that have **Show in search** enabled and that the visitor can view.
Every search term must match at least one of the page's title, menu title,
meta description, or content. Search terms are applied through parameterized
ORM filters. The shared search field label uses the site title from SiteConfig
when configured. Desktop autocomplete shows up to five suggestions after
three characters. Up and Down move focus through suggestions. Enter or Right
Arrow opens the focused result; Enter in the field submits the search. The
results page displays ten title-sorted matches per page with pagination. Each
inline suggestion is an article with a heading and optional description. Suggestions
include decorative CMS page-type icon markup when available:
an image URL or a CMS icon class. The module provides markup and behavior
only; style the `hc-site-search` classes and supply any icon font in your theme.

The starter `Page.ss` renders search results. If your theme overrides it, the
non-AJAX search action passes results as `$SearchPageContent` and sets
`$SearchResultsPage`; render the results in your theme's page template, for
example:

```ss
<% if $SearchResultsPage %>
    $SearchPageContent
<% else %>
    $Layout
<% end_if %>
```

Pages with **Show in search** disabled receive a `robots` `noindex` meta tag.
Pages with a sitemap `Priority` of `-1` also receive `noindex` when that field
is present.

## Contact information

Run `dev/build?flush=1` after installation to add the `Contact info` settings
tab. It provides formal name, office address, office phone, fax, mobile phone,
and email fields, retaining the legacy SiteConfig field names and stored data.

Site settings use Silverstripe Draft/Live versioning. **Save draft** records
changes without updating the public site; **Publish** saves and publishes the
settings and any owned records. Both actions require the existing
`EDIT_SITECONFIG` permission; publishing also respects `canPublish()`.
The first `dev/build` preserves existing settings on Live and starts their
revision history. Later builds do not publish pending changes. This versions
the entire SiteConfig record, not only its contact fields.

Use `[contact_info]` in rich-text content to show the name, address, office
phone, fax, and email, or select entries with `include`:

```text
[contact_info include="phone"]
[contact_info include="phone,email"]
[contact_info include="address"]
```

Supported selections are `name`, `address`, `phone`, `fax`, and `email`.
Selecting an address also includes the formal name. A single selection
(except address) renders inline; multiple selections use the legacy vCard
markup. Mobile phone remains a settings field, as in the original shortcode.
Override `ContactInfo.ss` in your theme to customize the markup.

## Site logo

Site settings provide a Logo image upload on the main tab, stored in the
`logo` folder. `WithSiteLogoExtension` retains the legacy `Logo` relation and
`LogoID` values. Run `dev/build?flush=1` after adding this feature.

The starter headers render `$SiteConfig.Logo` at the configured
`$SiteConfig.LogoWidth`, which defaults to 256 pixels. Override it in your
project YAML config:

```yaml
Logicbrush\HeroContent\Model\WithSiteLogoExtension:
  logo_width: 320
```

The Bootstrap header derives its 2x `srcset` width from
`$SiteConfig.LogoSrcSetWidth`. Header overrides can use these getters as well.
The starter `Page.ss` uses the site title as the logo's alternative text and
falls back to the title when no logo is selected.

**Save draft** keeps the logo selection in Draft. **Publish** publishes the
settings and the owned logo image, making them available on Live.

## Social profiles

Site settings include a `Social profiles` tab for LinkedIn company, Facebook,
GitHub, Twitter/X, YouTube, Instagram, and Pinterest profiles. Enter a profile
ID or a full HTTP(S) URL. For YouTube, use a channel ID or a full URL for
channels using @handles.

Templates can use `$SiteConfig.LinkedInURL`, `$SiteConfig.FacebookURL`,
`$SiteConfig.GitHubURL`, `$SiteConfig.TwitterURL`, `$SiteConfig.YouTubeURL`,
`$SiteConfig.InstagramURL`, and `$SiteConfig.PinterestURL`; unset profiles
return no URL. Twitter IDs retain the legacy `twitter.com` URL format;
full `x.com` URLs are accepted unchanged. Discontinued Google+ is not included.

The extension retains the legacy fields (`LinkedInID`, `FacebookID`,
`GitHubID`, `TwitterID`, `YouTubeID`, `InstagramID`, and `PinterestID`) and
stored values. Run `dev/build?flush=1` after installation, adding profiles, or
moving the extension from application code. Publish Settings to make restored
or changed profiles available on Live. The starter `Footer.ss` renders text
links for configured social profiles. Themes can customize or replace that
markup.

## Mobile navigation

The optional off-canvas menu uses [a11y-dialog](https://a11y-dialog.netlify.app/)
8.1.4 (MIT licensed, bundled locally), without jQuery. Include
`MobileNavigationToggle` inside your header and `MobileNavigation` directly
inside `body`, after your page content. Add `data-mobile-navigation-desktop`
to the element containing your desktop navigation links:

```ss
<header>
    <nav aria-label="Main navigation">
        <% include MainNavigation MobileNavigationDesktop=1 %>
        <% include MobileNavigationToggle %>
    </nav>
</header>
<!-- Main content and footer -->
<% include MobileNavigation %>
```

Below 1024px, the enhanced menu replaces the marked desktop links. It includes
visible menu pages and their descendants, highlights the current page, and
supports Escape, trapped keyboard focus, and focus restoration. Without
JavaScript, desktop links remain available and the menu button stays hidden.
The hamburger and close icons have visible MENU and CLOSE captions, accessible
button labels, and visible keyboard focus indicators.
The footer is unaffected. Override the three `MobileNavigation*.ss` includes
in your theme to customize the markup; the module loads its CSS and JavaScript
only when `MobileNavigation` is included and menu pages exist. Include each
menu and toggle only once per page; the menu ID is `mobile-navigation`.

## Version history

### 2026.12 (In Progress)

- Dropped Silverstripe CMS 4 support; the module now requires CMS 5 and PHP 8.1
  or later.
- Expanded the module with ordered content blocks, page presentation and
  navigation settings, site search, mobile navigation, and versioned site
  contact, logo, and social-profile settings.
- Added starter page, header, breadcrumb, navigation, and footer templates.
  The module supplies footer navigation data, including eligible child pages
  and recent blog posts; mobile navigation links render configured page icons
  and call-to-action classes.
- Renamed the page's CMS tab to **Featured content**, the template accessor
  from `$AdditionalContent` to `$FeaturedContent`, and the model class from
  `AdditionalContent` to `FeaturedContent`. `dev/build` remaps old class names;
  database tables and fields are unchanged. Project code using the old model
  class must be updated.
- Renamed all extension classes to begin with `With`, including
  `WithAdditionalContentExtension` to `WithFeaturedContentExtension`. Projects
  that refer to old class names in custom PHP or YAML must update those
  references; database fields and stored data are unchanged.
- Made featured-image, logo, and hero-image widths configurable via YAML.
  Featured images default to 600 pixels, logos to 256 pixels, and hero images
  to 2400 pixels. Featured-image and logo high-density `srcset` widths are
  derived from their configured values.
- Removed bundled featured-content grid CSS and the legacy `.typography` and
  `.with-icon` classes from the featured-content templates; themes own the
  layout and icon styling.

### [v1.1.0](https://github.com/logicbrush/silverstripe-herocontent/tree/v1.1.0) (2024-09-25)

- Added Silverstripe CMS 5 support while retaining CMS 4 support.
- Raised the minimum PHP version from 7.2 to 7.4.

### [v1.0.1](https://github.com/logicbrush/silverstripe-herocontent/tree/v1.0.1) (2023-09-08)

- Lowered the minimum PHP version to 7.2. The release tag notes that PHP 7.2
  compatibility was not tested.

### [v1.0.0](https://github.com/logicbrush/silverstripe-herocontent/tree/v1.0.0) (2023-09-08)

- Initial release for Silverstripe CMS 4, with per-page hero background images
  and optional rotating slides.
