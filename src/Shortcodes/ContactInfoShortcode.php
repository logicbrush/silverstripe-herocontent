<?php
/**
 * src/Shortcodes/ContactInfoShortcode.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Shortcodes;

use SilverStripe\Core\Convert;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;

class ContactInfoShortcode {

	/**
	 *
	 * @param array   $args
	 * @return unknown
	 */
	public static function render(array $args): string
	{
		$config = SiteConfig::current_site_config();
		$data = [];
		$count = 0;
		foreach ([
				'name' => 'ContactName',
				'address' => 'OfficeAddr',
				'phone' => 'OfficePhone',
				'fax' => 'OfficeFax',
				'email' => 'Email',
			] as $include => $field) {
			if (isset($args['include']) && !str_contains((string) $args['include'], $include)) {
				continue;
			}
			$data[$field] = $config->getField($field);
			$count++;
			if ($include === 'address') {
				$data['ContactName'] = $config->ContactName;
				$data['OfficeAddr'] = DBField::create_field(
					'HTMLText',
					nl2br(Convert::raw2xml((string) $config->OfficeAddr))
				);
				if (isset($args['include'])) {
					$count++;
				}
			}
		}
		$data['Tag'] = $count === 1 ? 'span' : 'div';
		$data['VCard'] = $count !== 1;

		return (string) SSViewer::create('ContactInfo')->process(ArrayData::create($data));
	}


}
