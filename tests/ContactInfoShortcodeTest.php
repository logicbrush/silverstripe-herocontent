<?php

namespace Logicbrush\HeroContent\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\HTMLValue;
use SilverStripe\View\Parsers\ShortcodeParser;

class ContactInfoShortcodeTest extends SapphireTest
{
	public $usesDatabase = true;

	protected function setUp(): void
	{
		parent::setUp();
		$config = SiteConfig::current_site_config();
		$config->update([
			'ContactName' => 'Example & Co',
			'OfficeAddr' => "123 Main Street\nSuite <2>",
			'OfficePhone' => '555-0100',
			'OfficeFax' => '555-0101',
			'MobilePhone' => '555-0102',
			'Email' => 'hello@example.com',
		]);
		$config->write();
	}

	public function testPhoneShortcodeRendersInlineThroughRichText()
	{
		$output = DBField::create_field('HTMLText', '<p>Call [contact_info include="phone"] today.</p>')->forTemplate();
		$html = HTMLValue::create($output);
		$this->assertSame(1, $html->query('//p/span[@class="tel"]/span[@class="value"]')->length);
		$this->assertStringContainsString('555-0100', $output);
		$this->assertStringNotContainsString('vcard', $output);
		$this->assertStringNotContainsString('555-0101', $output);
		$this->assertStringNotContainsString('contact_info', $output);
	}

	public function testAddressIncludesNameAndEscapesHtml()
	{
		$output = ShortcodeParser::get('default')->parse('[contact_info include="address"]');
		$html = HTMLValue::create($output);
		$this->assertSame(1, $html->query('//div[@class="vcard"]/div[@class="fn"]')->length);
		$this->assertSame(1, $html->query('//div[@class="adr"]/br')->length);
		$this->assertStringContainsString('Example &amp; Co', $output);
		$this->assertStringContainsString('Suite &lt;2&gt;', $output);
		$this->assertStringNotContainsString('555-0100', $output);
	}

	public function testFullAndSelectedContactDetails()
	{
		$parser = ShortcodeParser::get('default');
		$html = HTMLValue::create($parser->parse('[contact_info]'));
		$this->assertSame(2, $html->query('//div[@class="vcard"]/div[@class="tel"]')->length);
		$this->assertSame('mailto:hello@example.com', $html->query('//a[@class="email"]')->item(0)->getAttribute('href'));
		$this->assertStringNotContainsString('555-0102', $html->getContent());

		$output = $parser->parse('[contact_info include="phone,email"]');
		$this->assertStringContainsString('vcard', $output);
		$this->assertStringContainsString('555-0100', $output);
		$this->assertStringContainsString('hello@example.com', $output);
		$this->assertStringNotContainsString('Example', $output);
	}

	public function testEmptyValuesAndEscaping()
	{
		$config = SiteConfig::current_site_config();
		$config->OfficePhone = '<script>alert(1)</script>';
		$config->Email = '';
		$config->write();
		$output = ShortcodeParser::get('default')->parse('[contact_info include="phone,email"]');
		$html = HTMLValue::create($output);
		$this->assertSame(0, $html->query('//script | //a')->length);
		$this->assertStringContainsString('&lt;script&gt;', $output);
	}

	public function testSettingsExposeLegacyContactFields()
	{
		$config = SiteConfig::current_site_config();
		$fields = $config->getCMSFields();
		foreach (['ContactName', 'OfficeAddr', 'OfficePhone', 'OfficeFax', 'MobilePhone', 'Email'] as $name) {
			$this->assertNotNull($fields->fieldByName('Root.ContactInfo.' . $name));
		}
		$this->assertSame('555-0100', $config->OfficePhone);
	}
}
