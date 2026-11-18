<?php

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class ContactInfoExtension extends DataExtension
{
	private static array $db = [
		'ContactName' => 'Varchar(50)',
		'OfficeAddr' => 'Varchar(255)',
		'OfficePhone' => 'Varchar(20)',
		'OfficeFax' => 'Varchar(20)',
		'MobilePhone' => 'Varchar(20)',
		'Email' => 'Varchar(255)',
	];

	public function updateCMSFields(FieldList $fields)
	{
		$fields->addFieldsToTab('Root.ContactInfo', [
			TextField::create('ContactName', 'Formal Name'),
			TextareaField::create('OfficeAddr', 'Office Address'),
			TextField::create('OfficePhone', 'Office Phone'),
			TextField::create('OfficeFax', 'Office Fax'),
			TextField::create('MobilePhone', 'Mobile Phone'),
			EmailField::create('Email', 'Email Address'),
		]);
	}
}
