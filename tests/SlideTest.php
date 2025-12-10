<?php
/**
 * tests/SlideTest.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\Slide;
use SilverStripe\Dev\SapphireTest;

class SlideTest extends SapphireTest
{

	/**
	 *
	 */
	public function testGetCMSFields() {
		$slide = new Slide();
		$fields = $slide->getCMSFields();
		$this->assertNotNull( $fields );
	}


}
