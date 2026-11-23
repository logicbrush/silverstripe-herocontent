<?php

/**
 * tests/DisplayTest.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\Slide;
use Logicbrush\HeroContent\Model\WithHeroContentExtension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;

/**
 * @todo Document this class.
 */
class DisplayTestPage extends SiteTree {
	private static $extensions = [
		WithHeroContentExtension::class,
	];
}


/**
 * @todo Document this class.
 */
class DisplayTest extends SapphireTest {
	public $usesDatabase = true;

	/**
	 * @todo Document this public method.
	 */
	public function testDisplayHeroContent() {
		$page = new DisplayTestPage();
		$page->write();

		$slide1 = new Slide();
		$slide1->PageID = $page->ID;
		$slide1->write();

		$this->assertNotEmpty( trim( $page->HeroContent()->value ) );
	}


	/**
	 * @todo Document this public method.
	 */
	public function testGetCMSFields() {
		$page = new DisplayTestPage();
		$fields = $page->getCMSFields();
		$this->assertNotNull( $fields );
	}


}
