<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class WithPageNavigationExtension extends DataExtension
{
	private static array $db = [
		'DisplayChildrenInFooter' => 'Boolean',
		'HideInFooter' => 'Boolean',
		'MenuIcon' => 'Varchar(50)',
		'MenuLinkCallToAction' => 'Boolean',
	];

	public function updateSettingsFields(FieldList $fields)
	{
		$fields->addFieldsToTab('Root.Settings', [
			TextField::create('MenuIcon', 'Menu icon')
				->setDescription('Font Awesome classes, e.g. fa-solid fa-desktop.'),
			CheckboxField::create('MenuLinkCallToAction', 'Display menu link as a call to action'),
			CheckboxField::create('HideInFooter', 'Hide in footer'),
			CheckboxField::create('DisplayChildrenInFooter', 'Show subpages in footer'),
		]);
	}
}
