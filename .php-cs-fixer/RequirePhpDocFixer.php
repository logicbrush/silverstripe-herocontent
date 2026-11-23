<?php

declare( strict_types=1 );

namespace Logicbrush\Build\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;

/**
 * Adds PHPDoc skeletons to named classes and public methods.
 */
final class RequirePhpDocFixer extends AbstractFixer implements WhitespacesAwareFixerInterface {
	/** {@inheritDoc} */
	public function getName(): string {
		return 'Logicbrush/require_phpdoc';
	}

	/** {@inheritDoc} */
	public function getDefinition(): FixerDefinitionInterface {
		return new FixerDefinition(
			'Named classes and public methods must have non-empty PHPDoc.',
			[new CodeSample( "<?php\nclass Example {\n    public function run(): void {}\n}\n" )],
		);
	}

	/**
	 * Run before built-in PHPDoc and indentation rules.
	 */
	public function getPriority(): int {
		return 1000;
	}

	/** {@inheritDoc} */
	public function isCandidate( Tokens $tokens ): bool {
		return $tokens->isAnyTokenKindsFound( [T_CLASS, T_FUNCTION] );
	}

	protected function applyFix( SplFileInfo $file, Tokens $tokens ): void {
		$analyzer = new TokensAnalyzer( $tokens );
		$declarations = [];

		foreach ( $tokens as $index => $token ) {
			if ( $token->isGivenKind( T_CLASS ) && !$analyzer->isAnonymousClass( $index ) ) {
				$declarations[$index] = false;
			}
		}

		foreach ( $analyzer->getClassyElements() as $index => $element ) {
			if ( $element['type'] !== 'method' ) {
				continue;
			}

			$visibility = $analyzer->getMethodAttributes( $index )['visibility'];
			if ( $visibility === null || $visibility === T_PUBLIC ) {
				$declarations[$index] = true;
			}
		}

		krsort( $declarations );

		foreach ( $declarations as $index => $isMethod ) {
			$start = $index;
			$cursor = $index;
			$docIndex = null;

			while ( ( $previous = $tokens->getPrevNonWhitespace( $cursor ) ) !== null ) {
				$cursor = $previous;
				$token = $tokens[$previous];
				if ( $token->isGivenKind( T_DOC_COMMENT ) ) {
					$docIndex = $previous;
					break;
				}
				if ( $token->isGivenKind( CT::T_ATTRIBUTE_CLOSE ) ) {
					$start = $tokens->findBlockStart( Tokens::BLOCK_TYPE_ATTRIBUTE, $previous );
					$cursor = $start;
				} elseif ( $token->isGivenKind( [T_ABSTRACT, T_FINAL, T_READONLY, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC] ) ) {
					$start = $previous;
				} elseif ( !$token->isComment() ) {
					break;
				}
			}

			if ( $docIndex !== null && !preg_match( '/\A\/\*\*[\s*]*\*\/\z/', $tokens[$docIndex]->getContent() ) ) {
				continue;
			}

			$position = $docIndex ?? $start;
			$before = $position > 0 ? $tokens[$position - 1]->getContent() : '';
			preg_match( '/(?:^|\R)([\t ]*)$/', $before, $matches );
			$indent = $matches[1] ?? '';
			$lineEnding = $this->whitespacesConfig->getLineEnding();
			$lines = ['/**', ' * @todo Document this ' . ( $isMethod ? 'public method.' : 'class.' )];
			$tags = $isMethod ? $this->methodTags( $tokens, $index ) : [];
			if ( $tags !== [] ) {
				$lines[] = ' *';
				foreach ( $tags as $tag ) {
					$lines[] = ' * ' . $tag;
				}
			}
			$lines[] = ' */';
			$doc = new Token( [T_DOC_COMMENT, implode( $lineEnding . $indent, $lines )] );

			if ( $docIndex !== null ) {
				$tokens[$docIndex] = $doc;
				continue;
			}

			$insert = [$doc, new Token( [T_WHITESPACE, $lineEnding . $indent] )];
			if ( !preg_match( '/\R[\t ]*$/', $before ) ) {
				array_unshift( $insert, new Token( [T_WHITESPACE, $lineEnding . $indent] ) );
			}
			$tokens->insertAt( $start, $insert );
		}
	}

	/**
	 * @return list<string>
	 */
	private function methodTags( Tokens $tokens, int $index ): array {
		$analyzer = new FunctionsAnalyzer();
		$tags = [];
		foreach ( $analyzer->getFunctionArguments( $tokens, $index ) as $argument ) {
			$type = $argument->getTypeAnalysis();
			if ( $type === null ) {
				continue;
			}
			$previous = $tokens->getPrevMeaningfulToken( $argument->getNameIndex() );
			$variadic = $tokens[$previous]->isGivenKind( T_ELLIPSIS ) ? '...' : '';
			$tags[] = '@param ' . $this->phpDocType( $type ) . ' ' . $variadic . $argument->getName();
		}

		$returnType = $analyzer->getFunctionReturnType( $tokens, $index );
		if ( $returnType !== null ) {
			$tags[] = '@return ' . $this->phpDocType( $returnType );
		}
		return $tags;
	}

	private function phpDocType( TypeAnalysis $type ): string {
		$name = $type->getName();
		if ( $type->isNullable() && !in_array( 'null', explode( '|', $name ), true ) ) {
			$name .= '|null';
		}
		return $name;
	}
}
