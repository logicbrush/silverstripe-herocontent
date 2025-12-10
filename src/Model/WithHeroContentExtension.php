<?php
/**
 * src/Model/WithHeroContentExtension.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Model;

use SilverStripe\ORM\HasManyList;
use Logicbrush\HeroContent\Tests\DisplayTestPage;
use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\Tab;
use SilverStripe\Model\ArrayData;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 *
 * @property int $HeroImageID
 * @method Image HeroImage()
 * @method HasManyList<Slide> Slides()
 * @extends Extension<(DisplayTestPage&static | Page&static)>
 * @extends Extension<((DisplayTestPage & static) | (Page & static))>
 */
class WithHeroContentExtension extends Extension
{

	/**
	 *
	 * @Metrics( crap = 2.03 )
	 * @return unknown
	 */
	public function HeroContent() {
		if ( $this->getOwner()->Slides()->exists() ) {
			return ArrayData::create( [
					'Slides' => $this->getOwner()->Slides(),
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
				'Background Image'
			)
		);
		$field->setAllowedFileCategories( 'image' );

		// Add the slide gridfield.
		$slideFieldConfig = GridFieldConfig_RecordEditor::create();
		$slideFieldConfig->addComponent( GridFieldOrderableRows::create( 'SortOrder' ) );
		$dataColumns = $slideFieldConfig->getComponentByType( GridFieldDataColumns::class );
		$dataColumns->setFieldCasting( [
				'Content' => 'HTMLText->RAW',
			] );
		$slideField = GridField::create(
			'Slides',
			'Slides',
			$this->getOwner()->Slides(),
			$slideFieldConfig
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
