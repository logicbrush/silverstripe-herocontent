<?php
/**
 * tests/FeaturedContentTest.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Tests;

use Logicbrush\HeroContent\Model\FeaturedContent;
use Logicbrush\HeroContent\Model\WithFeaturedContentExtension;
use Logicbrush\HeroContent\Forms\SingleValueTagField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
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
		WithFeaturedContentExtension::class,
	];
}


class FeaturedContentTest extends SapphireTest
{
	public $usesDatabase = true;

	/**
	 *
	 */
	public function testImageWidthsCanBeConfigured(): void {
		Config::nest();
		try {
			Config::modify()->set(FeaturedContent::class, 'image_width', 800);
			$block = FeaturedContent::create();
			$this->assertSame(800, $block->getImageWidth());
			$this->assertSame(1600, $block->getImageSrcSetWidth());
		} finally {
			Config::unnest();
		}


	}


	/**
	 *
	 */
	public function testCanManageContentBlocks() {
		$page = new ContentBlocksTestPage();
		$page->write();

		$block = new FeaturedContent();
		$block->Content = '<p>Featured content</p>';
		$block->Style = 'existing-style';
		$page->ContentBlocks()->add($block);

		$this->assertCount(1, $page->ContentBlocks());
		$this->assertStringContainsString('Featured content', (string) $page->FeaturedContent());
		$this->assertNotNull($page->getCMSFields()->fieldByName('Root.FeaturedContent.ContentBlocks'));
		$this->assertInstanceOf(DBVarchar::class, $block->dbObject('Style'));
		$styleField = $block->getCMSFields()->fieldByName('Root.Main.Style');
		$this->assertInstanceOf(TagField::class, $styleField);
		$this->assertInstanceOf(SingleValueTagField::class, $styleField);
		$this->assertFalse($styleField->getIsMultiple());
		$this->assertTrue($styleField->getCanCreate());
		$this->assertTrue($styleField->getSourceList()->filter('Style', 'existing-style')->exists());

		$existingBlockCount = FeaturedContent::get()->count();
		$styleField->setValue('new-style');
		$styleField->saveInto($block);
		$this->assertSame('new-style', $block->Style);
		$this->assertSame($existingBlockCount, FeaturedContent::get()->count());
	}


	/**
	 *
	 */
	public function testLegacyClassNameIsMappedToNamespacedModel() {
		$mapping = DatabaseAdmin::config()->get('classname_value_remapping');

		$this->assertSame(FeaturedContent::class, $mapping['ContentBlock']);
		$this->assertSame(FeaturedContent::class, $mapping['Logicbrush\HeroContent\Model\AdditionalContent']);
		$this->assertSame('ContentBlock', DataObject::getSchema()->tableName(FeaturedContent::class));
	}


	/**
	 *
	 */
	public function testEditFormLoadsStoredStyle() {
		$block = FeaturedContent::create(['Style' => 'existing-style']);
		$block->write();
		$block = FeaturedContent::get()->byID($block->ID);

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
	 *
	 * @dataProvider blockClasses
	 * @param string  $classes
	 */
	public function testSavesAndRendersClassesOnIndividualBlocks(string $classes) {
		$page = ContentBlocksTestPage::create();
		$page->write();
		$block = FeaturedContent::create([
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
		$this->assertSame($classes, FeaturedContent::get()->byID($block->ID)->AdditionalCSSClasses);
		$page->ContentBlocks()->add(FeaturedContent::create([
					'Content' => '<p>Ordinary block</p>',
					'Columns' => '6',
					'Style' => 'class-test',
					'CssID' => 'ordinary-block',
				]));

		$html = HTMLValue::create((string) $page->FeaturedContent());
		$styled = $html->query('//*[@id="styled-block"]')->item(0);
		$this->assertSame('featured ' . $classes, $styled->getAttribute('class'));
		$this->assertFalse($styled->hasAttribute('onclick'));
		$this->assertSame(1, $html->query('//*[@id="styled-block"]/*[contains(concat(" ", normalize-space(@class), " "), " fa-icon ")]')->length);
		$this->assertSame('featured', $html->query('//*[@id="ordinary-block"]')->item(0)->getAttribute('class'));
		$this->assertSame(1, $html->query('//section[contains(concat(" ", normalize-space(@class), " "), " class-test ")]')->length);
	}


	/**
	 *
	 * @return unknown
	 */
	public function blockClasses(): array {
		return [
			'multiple classes' => ['no-card text-center custom-section'],
			'escaped attribute value' => ['custom" onclick="alert(1)'],
		];
	}


	/**
	 *
	 */
	public function testBlockClassesRespectDraftAndLive() {
		Versioned::withVersionedMode(function () {
				Versioned::set_stage(Versioned::DRAFT);
				$block = FeaturedContent::create(['AdditionalCSSClasses' => 'featured']);
				$block->write();
				$block->publishSingle();
				$block->AdditionalCSSClasses = 'no-card text-center';
				$block->write();

				$this->assertSame('no-card text-center', Versioned::get_by_stage(FeaturedContent::class, Versioned::DRAFT)->byID($block->ID)->AdditionalCSSClasses);
				$this->assertSame('featured', Versioned::get_by_stage(FeaturedContent::class, Versioned::LIVE)->byID($block->ID)->AdditionalCSSClasses);
			});
	}


}
