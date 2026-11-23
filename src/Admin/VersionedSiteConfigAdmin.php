<?php

/**
 * src/Admin/VersionedSiteConfigAdmin.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Admin;

use SilverStripe\Forms\Form;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Permission;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\SiteConfig\SiteConfigLeftAndMain;
use SilverStripe\Versioned\Versioned;

/**
 * @todo Document this class.
 */
class VersionedSiteConfigAdmin extends SiteConfigLeftAndMain {
	private static $ignore_menuitem = true;

	private static $allowed_actions = [
		'save_siteconfig',
		'publish_siteconfig',
	];

	/**
	 *
	 * @param unknown $data
	 * @param unknown $form
	 * @return unknown
	 */
	public function save_siteconfig( $data, $form ) {
		return $this->saveSettings( $form, false );
	}


	/**
	 *
	 * @param unknown $data
	 * @param unknown $form
	 * @return unknown
	 */
	public function publish_siteconfig( $data, $form ) {
		return $this->saveSettings( $form, true );
	}


	/**
  *
  * @return unknown
  */
	private function saveSettings( Form $form, bool $publish ) {
		return Versioned::withVersionedMode( function () use ( $form, $publish ) {
			Versioned::set_stage( Versioned::DRAFT );
			$config = SiteConfig::current_site_config();
			$data = $form->getData();
			if ( (int) ( $data['ID'] ?? 0 ) !== (int) $config->ID ) {
				$this->httpError( 400, 'Invalid site configuration ID.' );
			}
			if ( !Permission::check( 'EDIT_SITECONFIG' ) || !$config->canEdit() || ( $publish && !$config->canPublish() ) ) {
				$this->httpError( 403, 'You do not have permission to update these site settings.' );
			}
			DB::get_conn()->withTransaction( function () use ( $config, $form, $publish ) {
				$form->saveInto( $config );
				$config->write();
				if ( $publish && !$config->publishRecursive() ) {
					$this->httpError( 500, 'Could not publish site settings.' );
				}
			}, null, false, true );
			$message = $publish
			? _t( self::class . '.PUBLISHED', 'Site settings published.' )
			: _t( self::class . '.DRAFTSAVED', 'Draft saved. Publish to update the live site.' );
			$this->getResponse()->addHeader( 'X-Status', rawurlencode( $message ) );
			return $this->getEditForm()->forTemplate();
		} );
	}


}
