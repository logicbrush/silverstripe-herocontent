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
