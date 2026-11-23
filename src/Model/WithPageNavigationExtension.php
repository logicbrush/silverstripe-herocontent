<?php

/**
 * src/Model/WithPageNavigationExtension.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Model;

use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\ArrayData;

/**
 * @todo Document this class.
 */
class WithPageNavigationExtension extends DataExtension {
	private static array $db = [
		'DisplayChildrenInFooter' => 'Boolean',
		'HideInFooter' => 'Boolean',
		'MenuIcon' => 'Varchar(50)',
		'MenuLinkCallToAction' => 'Boolean',
	];

	/**
	 *
	 * @Metrics( crap = 30, uncovered = true )
	 * @param unknown $tagString
	 */
	public function MetaTags( string &$tagString ): void {
		$priorityExcluded = $this->owner->hasField( 'Priority' )
			&& (int) $this->owner->getField( 'Priority' ) === -1;
		$alreadyExcluded = preg_match(
			'/<meta\b[^>]*\bname=["\']robots["\'][^>]*\bcontent=["\'][^"\']*noindex/i',
			$tagString,
		) === 1;

		if ( ( !$this->owner->ShowInSearch || $priorityExcluded ) && !$alreadyExcluded ) {
			$tagString .= "\n<meta name=\"robots\" content=\"noindex\">";
		}
	}




	/**
  * @todo Document this public method.
  *
  * @Metrics( crap = 1 )
  */
	public function updateSettingsFields( FieldList $fields ) {
		$fields->addFieldsToTab( 'Root.Settings', [
			TextField::create( 'MenuIcon', 'Menu icon' )
			->setDescription( 'Font Awesome classes, e.g. fa-solid fa-desktop.' ),
			CheckboxField::create( 'MenuLinkCallToAction', 'Display menu link as a call to action' ),
			CheckboxField::create( 'HideInFooter', 'Hide in footer' ),
			CheckboxField::create( 'DisplayChildrenInFooter', 'Show subpages in footer' ),
		] );
	}


	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 * @return unknown
	 */
	public function FooterMenu(): ArrayList {
		return $this->footerPages( 0 );
	}


	/**
	 *
	 * @Metrics( crap = 12, uncovered = true )
	 * @return unknown
	 */
	public function FooterMenuColumns(): ArrayList {
		$pages = $this->FooterMenu();
		$columns = ArrayList::create();
		if ( !$pages->exists() ) {
			return $columns;
		}

		$columnSize = (int) ceil( $pages->count() / 3 );
		foreach ( array_chunk( $pages->toArray(), $columnSize ) as $column ) {
			$columns->push( ArrayData::create( [
				'Pages' => ArrayList::create( $column ),
			] ) );
		}

		return $columns;
	}


	/**
	 *
	 * @Metrics( crap = 12, uncovered = true )
	 * @return unknown
	 */
	public function FooterChildren(): ArrayList {
		if ( !$this->owner->DisplayChildrenInFooter ) {
			return ArrayList::create();
		}

		if ( $this->owner->hasMethod( 'getBlogPosts' ) ) {
			return $this->owner->getBlogPosts()
			->filter( 'HideInFooter', false )
			->limit( 3 )
			->filterByCallback( static fn( SiteTree $page ): bool => $page->canView() );
		}

		return $this->footerPages( (int) $this->owner->ID );
	}


	/**
  *
  * @Metrics( crap = 2, uncovered = true )
  * @return unknown
  */
	private function footerPages( int $parentID ): ArrayList {
		return Page::get()
		->filterAny( ['ShowInMenus' => true, 'ShowInSearch' => true] )
		->filter( [
			'ParentID' => $parentID,
			'HideInFooter' => false,
		] )
		->filterByCallback( static fn( SiteTree $page ): bool => $page->canView() );
	}


}
