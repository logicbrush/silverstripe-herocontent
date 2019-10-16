<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class Slide extends DataObject {

	public function getCMSFields() {

		$fields = FieldList::create();
		$fields->push(TabSet::create("Root"));

		$fields->addFieldToTab(
			'Root.Main',
			$field = HTMLEditorField::create('Content', 'Content')
		);

		$fields->addFieldToTab(
			'Root.Main',
			$field = UploadField::create( 'Image', 'Image' )
		);
		$field->setFolderName( 'slides' );
		$field->getValidator()->setAllowedMaxFileSize( 10485760 );

		$fields->addFieldToTab(
			'Root.Advanced',
			$field = TextareaField::create(
				'AdditionalHTML',
				'Additional HTML'
			)
		);
		$field->setRightTitle('You can add additional HTML code to the slide her.');
		$field->setRows(10);

		return $fields;
	}
	
	private static $singular_name = 'Slide';
	private static $plural_name = 'Slides';
	private static $table_name = 'Slide';

	private static $default_sort = 'SortOrder ASC';
	
	private static $extensions = [
		Versioned::class,
	];
	
	private static $summary_fields = [
		'Image.CMSThumbnail',
		'Content',
	];

	private static $field_labels = [
		'Image.CMSThumbnail' => 'Image',
	];

	private static $has_one = [
		'Image' => Image::class,
		'Page' => Page::class,
	];

	private static $owns = [
		'Image',
	];

	private static $db = [
		'Content' => 'HTMLText',
		'AdditionalHTML' => 'HTMLText',
		'SortOrder' => 'Int',
	];

}
