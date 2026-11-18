<?php

namespace Logicbrush\HeroContent\Model;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Versioned\Versioned;

/**
 * This class represents a block of content for the home page (or
 * pages similar to it). It's designed to be reorderable, have an
 * optional background image and provide a few different options for
 * display.
 *
 * @property ?string $Content
 * @property ?string $Columns
 * @property ?string $Style
 * @property ?string $Icon
 * @property ?string $CssID
 * @property ?string $HTML
 * @property int $SortOrder
 * @property int $PageID
 * @property int $ImageID
 * @method Page Page()
 * @method Image Image()
 * @mixin Versioned
 */
class AdditionalContent extends DataObject {
	private static $last_style = "\t";

	private static string $singular_name = 'Content Block';
	private static string $plural_name = 'Content Blocks';
	private static string $table_name = 'ContentBlock';

	private static array $extensions = [
		Versioned::class,
	];

	private static array $db = [
		'Content' => 'HTMLText',
		'Columns' => "Enum('all,12,11,10,9,8,7,6,5,4,3,2,1','all')",
		'Style' => "Enum(',content-blocks-style-1,content-blocks-style-2,content-blocks-style-3,content-blocks-style-4','')",
		'Icon' => 'Varchar(50)',
		'CssID' => 'Varchar(25)',
		'HTML' => 'Text',
		'SortOrder' => 'Int',
	];

	private static array $has_one = [
		'Page' => Page::class,
		'Image' => Image::class,
	];

	private static array $summary_fields = [
		'GridColumnContent',
		'Columns',
		'Style',
		'IconHTML',
		'CssID',
	];

	private static array $searchable_fields = [
		'Content',
	];

	private static array $field_labels = [
		'GridColumnContent' => 'Content',
		'Style' => 'Group Style',
		'IconHTML' => 'Icon',
	];

	private static array $owns = [
		'Image',
	];

	private static string $default_sort = 'SortOrder ASC';

	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeByName( 'SortOrder' );
		$fields->removeByName( 'PageID' );

		$fields->insertAfter(
			'Columns',
			DropdownField::create( 'Style', 'Group Style', singleton( AdditionalContent::class )->dbObject( 'Style' )->enumValues() )
			->setEmptyString( '' ),
		);

		$fields->insertBefore(
			'Style',
			TextField::create( 'Icon', 'Icon' ),
		);
		$fields->addFieldToTab(
			'Root.Extra',
			$field = TextareaField::create( 'HTML', 'Extra HTML' ),
		);
		$field->setRows( 20 );

		return $fields;
	}

	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 */
	public function IsFirstBlockInStyle() {
		$value = $this->Style != self::config()->get( 'last_style' );
		self::config()->set( 'last_style', $this->Style );

		return $value;
	}

	/**
	 *
	 * @Metrics( crap = 20, uncovered = true )
	 */
	public function HasIcons() {
		foreach ( $this->Page()->ContentBlocks() as $block ) {
			if ( $block->Style != $this->Style ) {
				continue;
			}
			if ( $block->Icon ) {
				return true;
			}
		}
		return null;
	}

	/**
	 *
	 * @Metrics( crap = 6, uncovered = true )
	 */
	public function getIconHTML() {
		if ( ! $this->Icon ) {
			return false;
		}

		return DBField::create_field( 'HTMLText', '<span class="fa-icon ' . $this->Icon . '" aria-hidden="true"></span>' );
	}

	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 */
	public function GridColumnContent() {
		return str_replace(
			'[user_form',
			'[<!-- -->user_form',
			(string) $this->Content,
		);
	}
}
