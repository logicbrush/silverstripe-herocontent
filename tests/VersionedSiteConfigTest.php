<?php

namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Admin\VersionedSiteConfigAdmin;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\DB;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\SiteConfig\SiteConfigLeftAndMain;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Parsers\ShortcodeParser;

class VersionedSiteConfigTest extends FunctionalTest
{
	public $usesDatabase = true;

	protected function setUp(): void
	{
		parent::setUp();
		$this->logInWithPermission('ADMIN');
		Versioned::set_stage(Versioned::DRAFT);
	}

	public function testSettingsFormSavesDraftThenPublishes()
	{
		$config = SiteConfig::current_site_config();
		$config->OfficePhone = '555-0100';
		$config->write();
		$config->publishSingle();
		$originalVersion = (int) $config->Version;

		$this->assertInstanceOf(VersionedSiteConfigAdmin::class, Injector::inst()->get(SiteConfigLeftAndMain::class));
		$this->assertSame(200, $this->get('admin/settings')->getStatusCode());
		$this->assertStringContainsString('Save draft', $this->content());
		$this->assertStringContainsString('action_publish_siteconfig', $this->content());
		$response = $this->submitForm('Form_EditForm', 'action_save_siteconfig', ['OfficePhone' => '555-0200']);
		$this->assertSame(200, $response->getStatusCode());
		$this->assertGreaterThan($originalVersion, (int) Versioned::get_by_stage(SiteConfig::class, Versioned::DRAFT)->byID($config->ID)->Version);
		$this->assertSame('555-0100', Versioned::get_by_stage(SiteConfig::class, Versioned::LIVE)->byID($config->ID)->OfficePhone);

		$liveOutput = Versioned::withVersionedMode(function () {
			Versioned::set_stage(Versioned::LIVE);
			return ShortcodeParser::get('default')->parse('[contact_info include="phone"]');
		});
		$this->assertStringContainsString('555-0100', $liveOutput);
		$this->assertStringNotContainsString('555-0200', $liveOutput);

		$this->get('admin/settings');
		$response = $this->submitForm('Form_EditForm', 'action_publish_siteconfig', ['OfficePhone' => '555-0300']);
		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame('555-0300', Versioned::get_by_stage(SiteConfig::class, Versioned::LIVE)->byID($config->ID)->OfficePhone);
		$this->assertGreaterThanOrEqual(3, Versioned::get_all_versions(SiteConfig::class, $config->ID)->count());
	}

	public function testBuildMigratesLegacySettingsOnlyOnce()
	{
		$config = SiteConfig::current_site_config();
		$config->OfficePhone = '555-0100';
		$config->write();
		DB::prepared_query('UPDATE "SiteConfig" SET "Version" = 0 WHERE "ID" = ?', [$config->ID]);
		DB::prepared_query('DELETE FROM "SiteConfig_Live" WHERE "ID" = ?', [$config->ID]);
		$config->flushCache();

		ob_start();
		try {
			SiteConfig::singleton()->requireDefaultRecords();
			$this->assertSame('555-0100', Versioned::get_by_stage(SiteConfig::class, Versioned::LIVE)->byID($config->ID)->OfficePhone);
			$draft = Versioned::get_by_stage(SiteConfig::class, Versioned::DRAFT)->byID($config->ID);
			$draft->OfficePhone = '555-0200';
			$draft->write();
			SiteConfig::singleton()->requireDefaultRecords();
		} finally {
			ob_end_clean();
		}
		$this->assertSame('555-0100', Versioned::get_by_stage(SiteConfig::class, Versioned::LIVE)->byID($config->ID)->OfficePhone);
		$this->assertSame('555-0200', Versioned::get_by_stage(SiteConfig::class, Versioned::DRAFT)->byID($config->ID)->OfficePhone);
	}

	public function testSocialProfileFieldsSaveDraftThenPublish()
	{
		$config = SiteConfig::current_site_config();
		$profiles = [
			'TwitterID' => 'https://x.com/example',
			'YouTubeID' => 'https://www.youtube.com/@example',
			'InstagramID' => 'example',
			'PinterestID' => 'example',
		];
		foreach ($profiles as $field => $value) {
			$config->setField($field, 'original');
		}
		$config->write();
		$config->publishSingle();

		$this->get('admin/settings');
		$response = $this->submitForm('Form_EditForm', 'action_save_siteconfig', $profiles);
		$this->assertSame(200, $response->getStatusCode());
		$draft = Versioned::get_by_stage(SiteConfig::class, Versioned::DRAFT)->byID($config->ID);
		$live = Versioned::get_by_stage(SiteConfig::class, Versioned::LIVE)->byID($config->ID);
		foreach ($profiles as $field => $value) {
			$this->assertSame($value, $draft->getField($field));
			$this->assertSame('original', $live->getField($field));
		}

		$this->get('admin/settings');
		$response = $this->submitForm('Form_EditForm', 'action_publish_siteconfig', $profiles);
		$this->assertSame(200, $response->getStatusCode());
		$live = Versioned::get_by_stage(SiteConfig::class, Versioned::LIVE)->byID($config->ID);
		foreach ($profiles as $field => $value) {
			$this->assertSame($value, $live->getField($field));
		}
		$this->assertSame('https://x.com/example', $live->getTwitterURL());
		$this->assertSame('https://www.youtube.com/@example', $live->getYouTubeURL());
	}

	public function testSettingsFormRejectsAnotherRecordId()
	{
		$config = SiteConfig::current_site_config();
		$phone = $config->OfficePhone;
		$this->get('admin/settings');
		$response = $this->submitForm('Form_EditForm', 'action_save_siteconfig', [
			'ID' => $config->ID + 1,
			'OfficePhone' => '555-0200',
		]);
		$this->assertSame(400, $response->getStatusCode());
		$this->assertSame($phone, Versioned::get_by_stage(SiteConfig::class, Versioned::DRAFT)->byID($config->ID)->OfficePhone);
	}
}
