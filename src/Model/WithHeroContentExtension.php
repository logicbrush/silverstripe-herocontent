<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\DataExtension;

class WithHeroContentExtension extends DataExtension {

	private static $db = [
		'HeroContent' => 'HTMLText',
	];

	private static $has_one = [
		'HeroImage' => Image::class,
	];

	private static $owns = [
		'HeroImage',
	];

	public function updateCMSFields( FieldList $fields ) {

		// Add the image field.
		$fields->addFieldToTab(
			'Root.HeroContent',
			$field = UploadField::create(
				'HeroImage',
				'Background Image'
			)
		);
		$field->setAllowedFileCategories( 'image' );

		// Add the Content block
		$fields->addFieldToTab(
			'Root.HeroContent',
			$field = HTMLEditorField::create(
				'HeroContent',
				'Content'
			)
		);

	}


}
