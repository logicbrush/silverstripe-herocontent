<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class SiteLogoExtension extends DataExtension
{
	private static array $has_one = [
		'Logo' => Image::class,
	];

	private static array $owns = [
		'Logo',
	];

	public function updateCMSFields(FieldList $fields)
	{
		$fields->addFieldToTab(
			'Root.Main',
			UploadField::create('Logo', 'Logo')
				->setFolderName('logo')
				->setAllowedFileCategories('image')
		);
	}
}
