<?php

/**
 * src/Model/WithPageClassesExtension.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * @todo Document this class.
 */
class WithPageClassesExtension extends DataExtension {
	private static array $db = [
		'AdditionalCSSClasses' => 'Text',
	];



	/**
	 * @todo Document this public method.
	 *
	 * @param FieldList $fields
	 */
	public function updateSettingsFields( FieldList $fields ) {
		$fields->addFieldToTab(
			'Root.Settings',
			TextField::create( 'AdditionalCSSClasses', 'Additional CSS classes' )
			->setDescription( 'Space-separated CSS class names to add to this page, for example: service-page featured.' ),
		);
	}


}
