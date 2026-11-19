<?php

namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\AdditionalContent;
use Logicbrush\HeroContent\Model\WithAdditionalContentExtension;
use Logicbrush\HeroContent\Forms\SingleValueTagField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DatabaseAdmin;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\TagField\TagField;
use SilverStripe\View\Parsers\HTMLValue;
use SilverStripe\Versioned\Versioned;

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

	/**
	 * @dataProvider blockClasses
	 */
	public function testSavesAndRendersClassesOnIndividualBlocks(string $classes) {
		$page = ContentBlocksTestPage::create();
		$page->write();
		$block = AdditionalContent::create([
			'Content' => '<h2>Section heading</h2>',
			'Columns' => '6',
			'Style' => 'class-test',
			'CssID' => 'styled-block',
			'Icon' => 'fa-star',
		]);
		$page->ContentBlocks()->add($block);
		$form = Form::create(null, 'EditForm', $block->getCMSFields());
		$this->assertInstanceOf(TextField::class, $form->Fields()->fieldByName('Root.Extra.AdditionalCSSClasses'));
		$form->loadDataFrom($block);
		$form->Fields()->dataFieldByName('AdditionalCSSClasses')->setValue($classes);
		$form->saveInto($block);
		$block->write();
		$this->assertSame($classes, AdditionalContent::get()->byID($block->ID)->AdditionalCSSClasses);
		$page->ContentBlocks()->add(AdditionalContent::create([
			'Content' => '<p>Ordinary block</p>',
			'Columns' => '6',
			'Style' => 'class-test',
			'CssID' => 'ordinary-block',
		]));

		$html = HTMLValue::create((string) $page->AdditionalContent());
		$styled = $html->query('//*[@id="styled-block"]')->item(0);
		$this->assertSame('additional with-icon typography ' . $classes, $styled->getAttribute('class'));
		$this->assertFalse($styled->hasAttribute('onclick'));
		$this->assertSame('--additional-content-columns: 6', $styled->getAttribute('style'));
		$this->assertSame('additional typography', $html->query('//*[@id="ordinary-block"]')->item(0)->getAttribute('class'));
		$this->assertSame(1, $html->query('//section[contains(concat(" ", normalize-space(@class), " "), " class-test ")]')->length);
	}

	public function blockClasses(): array {
		return [
			'multiple classes' => ['no-card text-center custom-section'],
			'escaped attribute value' => ['custom" onclick="alert(1)'],
		];
	}

	public function testBlockClassesRespectDraftAndLive() {
		Versioned::withVersionedMode(function () {
			Versioned::set_stage(Versioned::DRAFT);
			$block = AdditionalContent::create(['AdditionalCSSClasses' => 'featured']);
			$block->write();
			$block->publishSingle();
			$block->AdditionalCSSClasses = 'no-card text-center';
			$block->write();

			$this->assertSame('no-card text-center', Versioned::get_by_stage(AdditionalContent::class, Versioned::DRAFT)->byID($block->ID)->AdditionalCSSClasses);
			$this->assertSame('featured', Versioned::get_by_stage(AdditionalContent::class, Versioned::LIVE)->byID($block->ID)->AdditionalCSSClasses);
		});
	}
}
