<?php

/**
 * tests/PageNavigationTest.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Tests;

use Page;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

/**
 * @todo Document this class.
 */
class PageNavigationTest extends FunctionalTest {
	public $usesDatabase = true;

	/**
	 * @todo Document this public method.
	 */
	public function testSettingsFormSavesNavigationWithoutPublishingDraftChanges() {
		$this->logInWithPermission( 'ADMIN' );
		Versioned::withVersionedMode( function () {
			Versioned::set_stage( Versioned::DRAFT );
			$page = Page::create( ['Title' => 'Navigation settings', 'MenuIcon' => 'far fa-desktop'] );
			$page->write();
			$page->publishSingle();

			$this->assertSame( 200, $this->get( 'admin/pages/settings/show/' . $page->ID )->getStatusCode() );
			foreach ( ['MenuIcon', 'MenuLinkCallToAction', 'HideInFooter', 'DisplayChildrenInFooter'] as $name ) {
				$this->assertStringContainsString( 'name="' . $name . '"', $this->content() );
				$this->assertSame(
					DataObject::getSchema()->tableName( Page::class ),
					DataObject::getSchema()->tableForField( Page::class, $name ),
				);
			}
			$response = $this->submitForm( 'Form_EditForm', 'action_save', [
				'MenuIcon' => 'far fa-comments',
				'MenuLinkCallToAction' => 1,
				'HideInFooter' => 1,
				'DisplayChildrenInFooter' => 1,
			] );
			$this->assertSame( 200, $response->getStatusCode() );
			$draft = Versioned::get_by_stage( Page::class, Versioned::DRAFT )->byID( $page->ID );
			$live = Versioned::get_by_stage( Page::class, Versioned::LIVE )->byID( $page->ID );
			$this->assertSame( 'far fa-comments', $draft->MenuIcon );
			$this->assertSame( 'far fa-desktop', $live->MenuIcon );
			foreach ( ['MenuLinkCallToAction', 'HideInFooter', 'DisplayChildrenInFooter'] as $name ) {
				$this->assertTrue( (bool) $draft->getField( $name ) );
				$this->assertFalse( (bool) $live->getField( $name ) );
			}

			$draft->publishSingle();
			$live = Versioned::get_by_stage( Page::class, Versioned::LIVE )->byID( $page->ID );
			$this->assertSame( 'far fa-comments', $live->MenuIcon );
			foreach ( ['MenuLinkCallToAction', 'HideInFooter', 'DisplayChildrenInFooter'] as $name ) {
				$this->assertTrue( (bool) $live->getField( $name ) );
			}
		} );
	}


}
