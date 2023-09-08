# SilverStripe-HeroContent

A module for the SilverStripe CMS which allows you to add a big block of
scrolling or static content at the top of the page.

## Why?

When creating landing pages, we often need to add a big block of exciting
content at the top of the page.  This module will let you turn any page into a
landing page.

## Installation

```sh
composer require "logicbrush/silverstripe-herocontent"
```

## General Usage

Installation of this module will add a `Hero content` tab to all of your pages
wihin the CMS.  Here, you can select a background image and set up zero or more
"slides" that will cycle through using Slick.js.

Add a reference to `$HeroImage` in your template file to reference the hero
background image.  We generally like to add this to our `body` tag, often with a
transparent overlay.  That way it will appear behind all content on the page,
not just any slides.  You could alternatively apply it to a containing `div`.

Use `$HeroContent` in your template to render your slides.  If you wish, you can
copy and and adjust the `Slides.ss` template file into your theme.
