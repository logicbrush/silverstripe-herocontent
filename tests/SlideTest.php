<?php

/**
 * tests/SlideTest.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\Slide;
use SilverStripe\Dev\SapphireTest;

/**
 * @todo Document this class.
 */
class SlideTest extends SapphireTest {
	/**
	 * @todo Document this public method.
	 */
	public function testGetCMSFields() {
		$slide = new Slide();
		$fields = $slide->getCMSFields();
		$this->assertNotNull( $fields );
	}


}
