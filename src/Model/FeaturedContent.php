<?php

/**
 * src/Model/FeaturedContent.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Model;

use Logicbrush\HeroContent\Forms\SingleValueTagField;
use Page;
use SilverStripe\Assets\Image;
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
 * @property ?string $AdditionalCSSClasses
 * @property ?string $HTML
 * @property int $SortOrder
 * @property int $PageID
 * @property int $ImageID
 * @method Page Page()
 * @method Image Image()
 * @mixin Versioned
 */
class FeaturedContent extends DataObject {
	private static $last_style = "\t";

	private static string $singular_name = 'Content Block';
	private static string $plural_name = 'Content Blocks';
	private static string $table_name = 'ContentBlock';
	private static int $image_width = 600;

	private static array $extensions = [
		Versioned::class,
	];

	private static array $db = [
		'Content' => 'HTMLText',
		'Columns' => "Enum('all,12,11,10,9,8,7,6,5,4,3,2,1','all')",
		'Style' => 'Varchar(50)',
		'Icon' => 'Varchar(50)',
		'CssID' => 'Varchar(25)',
		'AdditionalCSSClasses' => 'Text',
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
	 * @return unknown
	 */
	public function getImageWidth(): int {
		return (int) self::config()->get( 'image_width' );
	}


	/**
	 *
	 * @return unknown
	 */
	public function getImageSrcSetWidth(): int {
		return $this->getImageWidth() * 2;
	}


	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 * @return unknown
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeByName( 'SortOrder' );
		$fields->removeByName( 'PageID' );
		$fields->removeByName( 'AdditionalCSSClasses' );

		$fields->insertAfter(
			'Columns',
			SingleValueTagField::create(
				'Style',
				'Group Style',
				FeaturedContent::get()->exclude( 'Style', '' ),
				null,
				'Style',
			)
			->setIsMultiple( false )
			->setCanCreate( true )
			->setShouldLazyLoad( true ),
		);

		$fields->insertBefore(
			'Style',
			TextField::create( 'Icon', 'Icon' ),
		);
		$fields->addFieldToTab(
			'Root.Extra',
			TextField::create( 'AdditionalCSSClasses', 'Additional CSS classes' )
			->setDescription( 'Space-separated CSS class names for this content block\'s wrapper.' ),
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
	 * @return unknown
	 */
	public function IsFirstBlockInStyle() {
		$value = $this->Style != self::config()->get( 'last_style' );
		self::config()->set( 'last_style', $this->Style );

		return $value;
	}


	/**
	 *
	 * @Metrics( crap = 20, uncovered = true )
	 * @return unknown
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
	 * @return unknown
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
	 * @return unknown
	 */
	public function GridColumnContent() {
		return str_replace(
			'[user_form',
			'[<!-- -->user_form',
			(string) $this->Content,
		);
	}


}
