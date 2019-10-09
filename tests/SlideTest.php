<?php

namespace Logicbrush\HeroContent\Tests;

use SilverStripe\Dev\SapphireTest;
use Logicbrush\HeroContent\Model\Slide;

class SlideTest extends SapphireTest
{

	public function testGetCMSFields() {
		$slide = new Slide();
		$fields = $slide->getCMSFields();
		$this->assertNotNull( $fields );
	}


}
