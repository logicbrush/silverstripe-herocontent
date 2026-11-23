<?php
/**
 * src/Model/WithFeaturedContentExtension.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Model;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\ORM\GroupedList;
use SilverStripe\ORM\HasManyList;
use SilverStripe\View\ArrayData;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * This extension provides featured content blocks for the page,
 * which can be arranged dynamically in the CMS.
 *
 * @method HasManyList<FeaturedContent> ContentBlocks()
 * @extends Extension<Page&static>
 */
class WithFeaturedContentExtension extends Extension {
	private static array $has_many = [
		'ContentBlocks' => FeaturedContent::class . '.Page',
	];

	private static array $owns = [
		'ContentBlocks',
	];

	/**
	 *
	 * @return string|null
	 */
	public function FeaturedContent() {
		$contentBlocks = $this->getOwner()->ContentBlocks();
		if (!$contentBlocks->exists()) {
			return null;
		}

		return ArrayData::create( [
				'FeaturedContent' => $contentBlocks,
			] )->renderWith( 'FeaturedContent' );
	}


	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 * @return unknown
	 */
	public function getContentBlockGroups() {
		return GroupedList::create( $this->getOwner()->ContentBlocks() );
	}


	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 * @return unknown
	 */
	public function getContentBlockGroupsByStyle() {
		return $this->getOwner()->ContentBlockGroups->GroupedBy( "Style" );
	}


	/**
	 *
	 * @Metrics( crap = 1 )
	 * @param FieldList $fields
	 */
	public function updateCMSFields( FieldList $fields ) {
		$config = GridFieldConfig_RecordEditor::create();
		$config->addComponent( GridFieldOrderableRows::create( 'SortOrder' ) );
		$config->removeComponentsByType( GridFieldPageCount::class );
		$config->removeComponentsByType( GridFieldPaginator::class );
		$dataColumns = $config->getComponentByType( GridFieldDataColumns::class );
		$dataColumns->setFieldCasting( [
				'GridColumnContent' => 'HTMLText->RAW',
				'IconHTML' => 'HTMLText->RAW',
			] );

		$fields->addFieldToTab(
			'Root.FeaturedContent',
			GridField::create(
				'ContentBlocks',
				'Content Blocks',
				$this->getOwner()->ContentBlocks(),
				$config,
			),
		);
	}


	private static array $casting = [
		'FeaturedContent' => 'HTMLText',
	];
}
