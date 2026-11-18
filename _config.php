<?php

use Logicbrush\HeroContent\Shortcodes\ContactInfoShortcode;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')->register('contact_info', [ContactInfoShortcode::class, 'render']);
