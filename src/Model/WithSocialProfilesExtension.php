<?php

declare( strict_types=1 );

namespace Logicbrush\HeroContent\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Retain the site's existing social profile settings.
 */
class WithSocialProfilesExtension extends DataExtension {
	private static array $db = [
		'LinkedInID' => 'Varchar(255)',
		'FacebookID' => 'Varchar(255)',
		'GitHubID' => 'Varchar(255)',
		'TwitterID' => 'Varchar(255)',
		'YouTubeID' => 'Varchar(255)',
		'InstagramID' => 'Varchar(255)',
		'PinterestID' => 'Varchar(255)',
	];

	public function updateCMSFields( FieldList $fields ): void {
		$fields->addFieldsToTab( 'Root.SocialProfiles', [
			TextField::create( 'LinkedInID', 'LinkedIn company profile' ),
			TextField::create( 'FacebookID', 'Facebook profile' ),
			TextField::create( 'GitHubID', 'GitHub profile' ),
			TextField::create( 'TwitterID', 'Twitter / X profile' ),
			TextField::create( 'YouTubeID', 'YouTube channel' ),
			TextField::create( 'InstagramID', 'Instagram profile' ),
			TextField::create( 'PinterestID', 'Pinterest profile' ),
		] );
		foreach ( array_keys( self::$db ) as $name ) {
			$fields->dataFieldByName( $name )->setDescription( 'Enter a profile ID or a full HTTP(S) URL.' );
		}
		$fields->dataFieldByName( 'YouTubeID' )->setDescription( 'Enter a channel ID or a full HTTP(S) URL, including URLs for @handles.' );
	}

	public function getLinkedInURL(): ?string {
		return $this->profileURL( 'LinkedInID', 'https://www.linkedin.com/company/' );
	}

	public function getFacebookURL(): ?string {
		return $this->profileURL( 'FacebookID', 'https://www.facebook.com/' );
	}

	public function getGitHubURL(): ?string {
		return $this->profileURL( 'GitHubID', 'https://github.com/' );
	}

	public function getTwitterURL(): ?string {
		return $this->profileURL( 'TwitterID', 'https://twitter.com/' );
	}

	public function getYouTubeURL(): ?string {
		return $this->profileURL( 'YouTubeID', 'https://youtube.com/channel/' );
	}

	public function getInstagramURL(): ?string {
		return $this->profileURL( 'InstagramID', 'https://instagram.com/' );
	}

	public function getPinterestURL(): ?string {
		return $this->profileURL( 'PinterestID', 'https://pinterest.com/' );
	}

	private function profileURL( string $field, string $baseURL ): ?string {
		$value = trim( (string) $this->getOwner()->getField( $field ) );
		if ( $value === '' ) {
			return null;
		}
		return preg_match( '#^https?://#i', $value ) ? $value : $baseURL . $value;
	}
}
