(function () {
	"use strict";

	function initializeSiteSearch() {
		document.querySelectorAll("[data-site-search]").forEach(function (search) {
			var form = search.querySelector("[data-site-search-form]");
			var input = search.querySelector("[data-site-search-input]");
			var results = search.querySelector("[data-site-search-results]");
			var timer = null;
			var request = null;

			function clearResults() {
				if (request) {
					request.abort();
					request = null;
				}
				results.replaceChildren();
			}

			function loadResults() {
				var query = input.value.trim();
				if (query.length < 3 || !search.open) {
					clearResults();
					return;
				}

				if (request) {
					request.abort();
				}
				request = new AbortController();
				var currentRequest = request;
				var url = new URL(form.action, window.location.href);
				url.searchParams.set(input.name, query);

				fetch(url, {
					headers: { "X-Requested-With": "XMLHttpRequest" },
					signal: currentRequest.signal,
				})
					.then(function (response) {
						if (!response.ok) {
							throw new Error("Search suggestions request failed.");
						}
						return response.text();
					})
					.then(function (html) {
						if (request === currentRequest && search.open) {
							results.innerHTML = html;
						}
					})
					.catch(function (error) {
						if (
							error.name !== "AbortError" &&
							request === currentRequest &&
							search.open
						) {
							results.textContent =
								"Search suggestions could not be loaded. Press Enter to view all results.";
						}
					})
					.finally(function () {
						if (request === currentRequest) {
							request = null;
						}
					});
			}

			input.addEventListener("input", function () {
				window.clearTimeout(timer);
				clearResults();
				timer = window.setTimeout(loadResults, 250);
			});

			search.addEventListener("keydown", function (event) {
				if (
					!search.open ||
					event.defaultPrevented ||
					event.isComposing ||
					event.altKey ||
					event.ctrlKey ||
					event.metaKey ||
					event.shiftKey
				) {
					return;
				}

				if (event.key === "Escape") {
					event.preventDefault();
					search.open = false;
					search.querySelector("summary").focus();
					return;
				}

				var down = event.key === "ArrowDown";
				var right = event.key === "ArrowRight";
				if (!down && !right && event.key !== "ArrowUp") {
					return;
				}

				var links = Array.from(
					results.querySelectorAll(".hc-site-search-results > li > article > a[href]"),
				);
				var index = links.indexOf(event.target);
				if (!links.length || (index === -1 && (right || event.target !== input))) {
					return;
				}

				event.preventDefault();
				if (right) {
					links[index].click();
				} else if (event.target === input) {
					links[down ? 0 : links.length - 1].focus();
				} else if (!down && index === 0) {
					input.focus();
				} else if (!down || index < links.length - 1) {
					links[index + (down ? 1 : -1)].focus();
				}
			});

			search.addEventListener("toggle", function () {
				if (search.open) {
					input.focus();
					loadResults();
				} else {
					window.clearTimeout(timer);
					clearResults();
				}
			});

			document.addEventListener("click", function (event) {
				if (!search.contains(event.target)) {
					search.open = false;
				}
			});
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initializeSiteSearch);
	} else {
		initializeSiteSearch();
	}
})();
