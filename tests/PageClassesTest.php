<?php

namespace Logicbrush\HeroContent\Tests;

use Page;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Versioned\Versioned;

class PageClassesTest extends FunctionalTest
{
	public $usesDatabase = true;

	public function testSettingsFormSavesClassesWithoutPublishingDraftChanges()
	{
		$this->logInWithPermission('ADMIN');
		Versioned::withVersionedMode(function () {
			Versioned::set_stage(Versioned::DRAFT);
			$page = Page::create(['Title' => 'Styled page']);
			$page->write();
			$page->publishSingle();

			$this->assertSame(200, $this->get('admin/pages/settings/show/' . $page->ID)->getStatusCode());
			$this->assertStringContainsString('name="AdditionalCSSClasses"', $this->content());
			$response = $this->submitForm('Form_EditForm', 'action_save', [
				'AdditionalCSSClasses' => 'service-page featured',
			]);
			$this->assertSame(200, $response->getStatusCode());
			$draft = Versioned::get_by_stage(Page::class, Versioned::DRAFT)->byID($page->ID);
			$live = Versioned::get_by_stage(Page::class, Versioned::LIVE)->byID($page->ID);
			$this->assertSame('service-page featured', $draft->AdditionalCSSClasses);
			$this->assertEmpty($live->AdditionalCSSClasses);

			$draft->publishSingle();
			$live = Versioned::get_by_stage(Page::class, Versioned::LIVE)->byID($page->ID);
			$this->assertSame('service-page featured', $live->AdditionalCSSClasses);
		});
	}
}
