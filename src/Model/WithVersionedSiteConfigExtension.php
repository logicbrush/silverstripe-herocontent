<?php

/**
 * src/Model/WithVersionedSiteConfigExtension.php
 *
 * @package default
 */

namespace Logicbrush\HeroContent\Model;

use RuntimeException;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DB;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Versioned\Versioned;

/**
 * @todo Document this class.
 */
class WithVersionedSiteConfigExtension extends DataExtension {
	/**
	 * @todo Document this public method.
	 *
	 * @param FieldList $actions
	 */
	public function updateCMSActions( FieldList $actions ) {
		$save = $actions->fieldByName( 'action_save_siteconfig' );
		if ( !$save ) {
			return;
		}
		$save->setTitle( _t( self::class . '.SAVEDRAFT', 'Save draft' ) );
		$save->removeExtraClass( 'btn-primary' );
		$save->addExtraClass( 'btn-secondary' );
		if ( $this->owner->canPublish() ) {
			$actions->push(
				FormAction::create( 'publish_siteconfig', _t( self::class . '.PUBLISH', 'Publish' ) )
				->addExtraClass( 'btn-primary font-icon-rocket' ),
			);
		}
	}


	/**
	 * @todo Document this public method.
	 */
	public function requireDefaultRecords() {
		Versioned::withVersionedMode( function () {
			Versioned::set_stage( Versioned::DRAFT );
			$configs = SiteConfig::get();
			if ( !$configs->exists() ) {
				$config = SiteConfig::make_site_config();
				if ( !$config->publishSingle() ) {
					throw new RuntimeException( 'Could not publish the initial site configuration.' );
				}
				return;
			}
			foreach ( $configs->filter( 'Version', 0 ) as $config ) {
				$config->write();
				if ( !$config->publishSingle() ) {
					throw new RuntimeException( 'Could not publish the existing site configuration during versioning setup.' );
				}
				DB::alteration_message( 'Enabled Draft/Live versioning for existing site settings.', 'notice' );
			}
		} );
	}


}
