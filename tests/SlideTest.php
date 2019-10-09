<?php

namespace Logicbrush\HeroContent\Tests;

use SilverStripe\Dev\SapphireTest;
use Logicbrush\HeroContent\Model\Slide;

class SlideTest extends SapphireTest
{

	protected $usesDatabase = true;

	function testCanCreateSlide() {
		$slide = new Slide();
		$slide->write();

		$this->assertEquals( 1, Slide::get()->count() );
	}


	public function testGetCMSFields() {
		$slide = new Slide();
		$fields = $slide->getCMSFields();
		$this->assertNotNull( $fields );
		unset( $fields );
		unset( $slide );
	}


}
