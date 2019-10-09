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

    public function HeroContent() {
        if ($this->owner->Slides()->exists()) {
            $content = '';
            foreach($this->owner->Slides() as $slide) {
                if ($slide->Image) {
                    $content .= '<img src="' . $slide->Image->FocusFill(1200,400)->URL . '" alt="' . $slide->Title . '" />';
                }
            }
            return $content;
        }
    }

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

		$slideFieldConfig = GridFieldConfig_RecordEditor::create();
		$slideFieldConfig->addComponent( new GridFieldOrderableRows( 'SortOrder' ) );

		$slideField = GridField::create(
			'Slides',
			'Slides',
			$this->owner->Slides(),
			$slideFieldConfig
		);
		$fields->addFieldToTab( 'Root.HeroContent', $slideField );

	}

    private static $casts = [
        'HeroContent' => 'HTMLText',
    ];

	private static $has_many = [
		'Slides' => Slide::class . '.Page',
	];

	private static $has_one = [
		'HeroImage' => Image::class,
	];

    private static $owns = [
		'HeroImage',
        'Slides',
	];

}
