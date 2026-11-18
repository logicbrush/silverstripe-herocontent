<?php

namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\AdditionalContent;
use Logicbrush\HeroContent\Model\WithAdditionalContentExtension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DatabaseAdmin;

class ContentBlocksTestPage extends SiteTree
{
	private static $extensions = [
		WithAdditionalContentExtension::class,
	];
}

class AdditionalContentTest extends SapphireTest
{
	public $usesDatabase = true;

	public function testCanManageContentBlocks() {
		$page = new ContentBlocksTestPage();
		$page->write();

		$block = new AdditionalContent();
		$block->Content = '<p>Additional content</p>';
		$page->ContentBlocks()->add($block);

		$this->assertCount(1, $page->ContentBlocks());
		$this->assertStringContainsString('Additional content', (string) $page->AdditionalContent());
		$this->assertNotNull($page->getCMSFields()->fieldByName('Root.AdditionalContent.ContentBlocks'));
	}

	public function testLegacyClassNameIsMappedToNamespacedModel() {
		$mapping = DatabaseAdmin::config()->get('classname_value_remapping');

		$this->assertSame(AdditionalContent::class, $mapping['ContentBlock']);
		$this->assertSame('ContentBlock', DataObject::getSchema()->tableName(AdditionalContent::class));
	}
}
