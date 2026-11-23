<?php

/**
 * src/Model/WithHeroContentExtension.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\ArrayData;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * @todo Document this class.
 */
class WithHeroContentExtension extends DataExtension {
	private static int $hero_image_width = 2400;

	/**
	 *
	 * @return unknown
	 */
	public function getHeroImageWidth(): int {
		return (int) Config::forClass( self::class )->get( 'hero_image_width' );
	}


	/**
	 *
	 * @Metrics( crap = 2 )
	 * @return unknown
	 */
	public function HeroContent() {
		if ( $this->owner->Slides()->exists() ) {
			return ArrayData::create( [
				'Slides' => $this->owner->Slides(),
			] )->renderWith( 'Slides' );
		}
		return null;
	}


	/**
  *
  * @Metrics( crap = 1 )
  */
	public function updateCMSFields( FieldList $fields ) {

		// Create the "Hero Content" tab.
		$fields->insertAfter( 'Main', Tab::create( $thisTabName = 'HeroContent' ) );

		// Add the image field.
		$fields->addFieldToTab(
			"Root.{$thisTabName}",
			$field = UploadField::create(
				'HeroImage',
				'Background Image',
			),
		);
		$field->setAllowedFileCategories( 'image' );

		// Add the slide gridfield.
		$slideFieldConfig = GridFieldConfig_RecordEditor::create();
		$slideFieldConfig->addComponent( new GridFieldOrderableRows( 'SortOrder' ) );
		$dataColumns = $slideFieldConfig->getComponentByType( GridFieldDataColumns::class );
		$dataColumns->setFieldCasting( [
			'Content' => 'HTMLText->RAW',
		] );
		$slideField = GridField::create(
			'Slides',
			'Slides',
			$this->owner->Slides(),
			$slideFieldConfig,
		);
		$fields->addFieldToTab( "Root.{$thisTabName}", $slideField );

	}


	private static $casting = [
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
