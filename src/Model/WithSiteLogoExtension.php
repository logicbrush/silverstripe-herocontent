<?php
/**
 * src/Model/WithSiteLogoExtension.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class WithSiteLogoExtension extends DataExtension
{
	private static int $logo_width = 256;

	private static array $has_one = [
		'Logo' => Image::class,
	];

	private static array $owns = [
		'Logo',
	];

	/**
	 *
	 * @return unknown
	 */
	public function getLogoWidth(): int
	{
		return (int) Config::forClass(self::class)->get('logo_width');
	}


	/**
	 *
	 * @return unknown
	 */
	public function getLogoSrcSetWidth(): int
	{
		return $this->getLogoWidth() * 2;
	}


	/**
	 *
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab(
			'Root.Main',
			UploadField::create('Logo', 'Logo')
			->setFolderName('logo')
			->setAllowedFileCategories('image')
		);
	}


}
