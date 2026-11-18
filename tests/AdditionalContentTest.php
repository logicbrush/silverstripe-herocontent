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
use SilverStripe\View\Parsers\HTMLValue;

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

	public function testRendersColumnSpansInPicoGrid() {
		$page = ContentBlocksTestPage::create();
		$page->write();
		foreach (['1', '6', '12', 'all'] as $columns) {
			$page->ContentBlocks()->add(AdditionalContent::create([
				'Content' => '<p>Grid content</p>',
				'Columns' => $columns,
				'CssID' => 'grid-block-' . $columns,
				'Style' => 'grid-test',
			]));
		}

		$html = HTMLValue::create((string) $page->AdditionalContent());
		$grid = $html->query('//div[contains(concat(" ", normalize-space(@class), " "), " additional-content-grid ")]');
		$this->assertSame(1, $grid->length);
		$this->assertSame(4, $html->query('//div[contains(concat(" ", normalize-space(@class), " "), " additional-content-grid ")]/div')->length);
		foreach (['1', '6', '12'] as $columns) {
			$block = $html->query('//*[@id="grid-block-' . $columns . '"]')->item(0);
			$this->assertSame('--additional-content-columns: ' . $columns, $block->getAttribute('style'));
		}
		$this->assertFalse($html->query('//*[@id="grid-block-all"]')->item(0)->hasAttribute('style'));
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
