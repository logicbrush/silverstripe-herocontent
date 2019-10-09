<?php

namespace Logicbrush\HeroContent\Tests;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;
use Logicbrush\HeroContent\Model\WithHeroContentExtension;
use Logicbrush\HeroContent\Model\Slide;
use SilverStripe\Assets\Image;

class DisplayTestPage extends SiteTree {

    private static $extensions = [
        WithHeroContentExtension::class
    ];
                             
}

class DisplayTest extends SapphireTest
{

    public $usesDatabase = true;

	public function testDisplayHeroContent() {

        $page = new DisplayTestPage();
        $page->write();
        
        $slide1 = new Slide();
        $slide1->PageID = $page->ID;
        $slide1->write();

        $this->assertNotEmpty(trim($page->HeroContent()->value));
	}


}
