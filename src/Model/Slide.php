<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

class Slide extends DataObject {

	public function getCMSFields() {
		$image = UploadField::create( 'Image', 'Image' );
		$image->setFolderName( 'slides' );
		$image->getValidator()->setAllowedMaxFileSize( 10485760 );

		return FieldList::create(
			$image
		);
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
		'SortOrder' => 'Int',
	];

}
