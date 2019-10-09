<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\DataExtension;

class WithHeroContentExtension extends DataExtension {

	private static $db = [
		//'HeroContent' => 'HTMLText',
	];

	private static $has_many = [
		'Slides' => Slide::class,
	];

	private static $has_one = [
		'HeroImage' => Image::class,
	];

    private static $owns = [
		'HeroImage',
        'Slide',
	];

	public function updateCMSFields( FieldList $fields ) {

		$slideFieldConfig = GridFieldConfig_RecordEditor::create();
		if ( $this->Slides()->count() > 0 ) {
            $slideFieldConfig->removeComponentsByType( GridFieldAddNewButton::class );
            $slideFieldConfig->removeComponentsByType( GridFieldAddExistingAutocompleter::class );
		}
		$slideField = GridField::create(
            'Slides',
            'Slide',
            $this->Slides(),
            $slideFieldConfig
		);
		$fields->addFieldToTab( 'Root.HeroContent', $slideField );

		// Add the image field.
		$fields->addFieldToTab(
			'Root.HeroContent',
			$field = UploadField::create(
				'HeroImage',
				'Background Image'
			)
		);
		$field->setAllowedFileCategories( 'image' );

		// // Add the Content block
		// $fields->addFieldToTab(
		// 	'Root.HeroContent',
		// 	$field = HTMLEditorField::create(
		// 		'HeroContent',
		// 		'Content'
		// 	)
		// );

	}


}
