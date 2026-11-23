<?php

declare( strict_types=1 );

use Logicbrush\Build\Fixer\RequirePhpDocFixer;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

require_once __DIR__ . '/.php-cs-fixer/RequirePhpDocFixer.php';

$finder = Finder::create()
	->in( __DIR__ )
	->ignoreDotFiles( false )
	->exclude( ['vendor', 'node_modules', 'build'] );

return ( new Config() )
	->registerCustomFixers( [new RequirePhpDocFixer()] )
	->setRules( [
		'@PER-CS' => true,
		'Logicbrush/require_phpdoc' => true,
		'spaces_inside_parentheses' => ['space' => 'single'],
		'braces_position' => [
			'classes_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line',
			'allow_single_line_anonymous_functions' => false,
		],
	] )
	->setRiskyAllowed( false )
	->setIndent( "\t" )
	->setCacheFile( __DIR__ . '/build/caches/.php-cs-fixer.cache' )
	->setFinder( $finder );
