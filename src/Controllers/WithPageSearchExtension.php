<?php
/**
 * src/Controllers/WithPageSearchExtension.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Controllers;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;

class WithPageSearchExtension extends Extension
{
	private static $allowed_actions = [
		'Search',
	];

	private const SUGGESTION_LIMIT = 5;

	/**
	 *
	 * @param unknown $instance (optional)
	 * @return unknown
	 */
	public function SiteSearch($instance = 'header') {
		if (!is_string($instance) || !preg_match('/\A[a-zA-Z0-9_-]+\z/', $instance)) {
			$instance = 'header';
		}

		$request = $this->owner->getRequest();
		$query = $request ? $request->getVar('Search') : '';

		return ArrayData::create([
				'SearchAction' => $this->owner->Link('Search'),
				'SearchClass' => 'hc-site-search--' . $instance,
				'SearchInputID' => $instance . '-search',
				'SearchSuggestionsID' => $instance . '-suggestions',
				'SearchHelpID' => $instance . '-help',
				'SearchValue' => is_string($query) ? $query : '',
				'SearchSiteName' => rtrim(trim((string) $this->owner->SiteConfig()->Title), '.'),
				'SearchAutocomplete' => $instance !== 'mobile',
			])->renderWith($instance === 'mobile' ? 'Includes/MobileSiteSearch' : 'Includes/SiteSearch');
	}


	/**
	 *
	 * @param HTTPRequest $request
	 * @return unknown
	 */
	public function Search(HTTPRequest $request) {
		$searchValue = $request->getVar('Search');
		$query = is_string($searchValue) ? trim($searchValue) : '';
		$results = $this->SiteSearchResults($query);

		if ($request->isAjax()) {
			$suggestions = ArrayList::create();
			$hasMore = false;

			foreach ($results->limit(self::SUGGESTION_LIMIT + 1) as $page) {
				if ($suggestions->count() >= self::SUGGESTION_LIMIT) {
					$hasMore = true;
					break;
				}
				$suggestions->push($page);
			}

			$viewMoreLink = $hasMore
				? $this->owner->Link('Search') . '?' . http_build_query(
				['Search' => $query],
				'',
				'&',
				PHP_QUERY_RFC3986
			)
				: null;

			return ArrayData::create([
					'Results' => $suggestions,
					'ViewMoreLink' => $viewMoreLink,
				])->renderWith('Includes/SiteSearchResults');
		}

		$paginatedResults = PaginatedList::create($results, $request);
		$paginatedResults->setPageLength(10);
		$pageContent = ArrayData::create([
				'Query' => $query,
				'Results' => $paginatedResults,
			])->renderWith('Includes/SiteSearchPage');

		return $this->owner->customise([
				'Title' => 'Search results',
				'SearchPageContent' => $pageContent,
				'SearchResultsPage' => true,
			])->renderWith('Page');
	}


	/**
	 *
	 * @param unknown $query
	 * @return unknown
	 */
	public function SiteSearchResults($query) {
		$pages = Versioned::get_by_stage(SiteTree::class, Versioned::LIVE)
		->filter('ShowInSearch', true);
		$query = is_string($query) ? trim($query) : '';
		if ($query === '') {
			return $pages->filter('ID', 0);
		}

		$words = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($words as $word) {
			$pages = $pages->filterAny([
					'Title:PartialMatch' => $word,
					'MenuTitle:PartialMatch' => $word,
					'MetaDescription:PartialMatch' => $word,
					'Content:PartialMatch' => $word,
				]);
		}

		return $pages
		->sort('Title', 'ASC')
		->filterByCallback(function (SiteTree $page) {
				return $page->canView();
			});
	}


}
