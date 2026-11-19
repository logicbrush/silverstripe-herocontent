# silverstripe-herocontent

A module for the SilverStripe CMS which allows you to add a big block of
(optionally rotating) content at the top of the page as is typical with landing
pages.

## Why?

When creating landing pages, we often want to add something flashy at the top of
the page to quickly capture visitor interest.  This module allows us to
configure this using a common backend, but customize display on a
client-by-client basis. It also lets you add and order additional content
blocks on pages.

## Installation

```sh
composer require "logicbrush/silverstripe-herocontent"
```

## General Usage

Installation of this module will add `Hero content` and `Additional content`
tabs to all of your pages within the CMS. In `Hero content`, you can select a
background image and set up zero or more "slides" that will rotate through
using Slick.js. Each slide consists of an image and a block of HTML content.

Add `$HeroImage` or `$HeroImage.URL` in your template file to reference the hero
background image. We frequently like to add it with inline CSS to our `body`
tag, usually with a transparent overlay and `background-size: cover` and
`background-attachment: fixed`.  That way it will appear behind all content on
the page and stick to the top of the browser window.  You could alternatively
apply it to an independent `div`.

Use `$HeroContent` in your template to render your slides.  If you wish, you can
copy the `Slides.ss` template file into your theme and modify to your liking.
You can place this either above or below your regular `$Content` as you like.
Or, you can leave it out entirely if you don't need rotating content.

In `Additional content`, you can create, edit, and order content blocks.
The module renders these by default with `AdditionalContent.ss`; copy that
template into your theme to customize the output. The module preserves the
existing `ContentBlock` database table and remaps stored legacy `ContentBlock`
class names to `AdditionalContent` during `dev/build`.

The default template retains Pico's `.container.grid` and gaps, extending it
to 12 columns at 768px and wider. Each block's `Columns` value sets its span:
`6` is half a row, `4` is one third, and `12` or `all` fills a row. Blocks
wrap in content order and stack full width below 768px. These scoped rules
are loaded from `client/dist/additional-content.css` by the template and
do not change other Pico grids.

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
or changed profiles available on Live. The module does not add social link
markup; render the desired links in your theme.

## Mobile navigation

The optional off-canvas menu uses [a11y-dialog](https://a11y-dialog.netlify.app/)
8.1.4 (MIT licensed, bundled locally), without jQuery. Include
`MobileNavigationToggle` inside your header and `MobileNavigation` directly
inside `body`, after your page content. Add `data-mobile-navigation-desktop`
to the element containing your desktop navigation links:

```ss
<header>
    <nav>
        <ul data-mobile-navigation-desktop>
            <% loop $Menu(1) %>
                <li><a href="$Link">$MenuTitle.XML</a></li>
            <% end_loop %>
        </ul>
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
