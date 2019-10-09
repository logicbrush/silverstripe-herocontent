<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\DataExtension;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

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
		$slideFieldConfig->addComponent( new GridFieldOrderableRows( 'SortOrder' ) );

		$slideField = GridField::create(
			'Slides',
			'Slide',
			$this->owner->Slides(),
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
