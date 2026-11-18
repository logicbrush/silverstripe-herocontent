<?php

namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\AdditionalContent;
use Logicbrush\HeroContent\Model\WithAdditionalContentExtension;
use Logicbrush\HeroContent\Forms\SingleValueTagField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\Form;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DatabaseAdmin;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\TagField\TagField;

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
		$block->Style = 'existing-style';
		$page->ContentBlocks()->add($block);

		$this->assertCount(1, $page->ContentBlocks());
		$this->assertStringContainsString('Additional content', (string) $page->AdditionalContent());
		$this->assertNotNull($page->getCMSFields()->fieldByName('Root.AdditionalContent.ContentBlocks'));
		$this->assertInstanceOf(DBVarchar::class, $block->dbObject('Style'));
		$styleField = $block->getCMSFields()->fieldByName('Root.Main.Style');
		$this->assertInstanceOf(TagField::class, $styleField);
		$this->assertInstanceOf(SingleValueTagField::class, $styleField);
		$this->assertFalse($styleField->getIsMultiple());
		$this->assertTrue($styleField->getCanCreate());
		$this->assertTrue($styleField->getSourceList()->filter('Style', 'existing-style')->exists());

		$existingBlockCount = AdditionalContent::get()->count();
		$styleField->setValue('new-style');
		$styleField->saveInto($block);
		$this->assertSame('new-style', $block->Style);
		$this->assertSame($existingBlockCount, AdditionalContent::get()->count());
	}

	public function testLegacyClassNameIsMappedToNamespacedModel() {
		$mapping = DatabaseAdmin::config()->get('classname_value_remapping');

		$this->assertSame(AdditionalContent::class, $mapping['ContentBlock']);
		$this->assertSame('ContentBlock', DataObject::getSchema()->tableName(AdditionalContent::class));
	}

	public function testEditFormLoadsStoredStyle() {
		$block = AdditionalContent::create(['Style' => 'existing-style']);
		$block->write();
		$block = AdditionalContent::get()->byID($block->ID);

		$form = Form::create(null, 'EditForm', $block->getCMSFields());
		$form->loadDataFrom($block);
		$field = $form->Fields()->dataFieldByName('Style');

		$this->assertSame(['existing-style'], $field->getValueArray());
		$this->assertSame('existing-style', $field->getSchemaStateDefaults()['value'][0]['Value']);
		$this->assertSame('existing-style', $field->getSchemaDataDefaults()['value'][0]['Value']);

		$field->saveInto($block);
		$this->assertSame('existing-style', $block->Style);

		$block->Style = null;
		$form->loadDataFrom($block);
		$this->assertSame([], $field->getValueArray());
		$this->assertNull($field->getSchemaStateDefaults()['value']);
	}
}
