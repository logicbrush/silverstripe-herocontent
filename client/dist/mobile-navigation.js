(function () {
	"use strict";

	function initializeMobileNavigation() {
		var container = document.querySelector("[data-mobile-navigation]");
		if (!container) {
			return;
		}

		var dialog = new A11yDialog(container);
		var triggers = document.querySelectorAll(
			'[data-a11y-dialog-show="' + container.id + '"]'
		);
		var desktop = window.matchMedia("(min-width: 1024px)");

		function updateExpanded(expanded) {
			triggers.forEach(function (trigger) {
				trigger.setAttribute("aria-expanded", String(expanded));
			});
			document.documentElement.classList.toggle(
				"hc-mobile-navigation-open", expanded
			);
		}

		dialog.on("show", function () {
			updateExpanded(true);
		});
		dialog.on("hide", function () {
			updateExpanded(false);
		});
		container.addEventListener("click", function (event) {
			if (event.target.closest("nav a[href]")) {
				dialog.hide();
			}
		});
		desktop.addEventListener("change", function (event) {
			if (event.matches && dialog.shown) {
				dialog.hide();
				var link = document.querySelector(
					"[data-mobile-navigation-desktop] a[href]"
				);
				if (link) {
					link.focus();
				}
			}
		});

		triggers.forEach(function (trigger) {
			trigger.hidden = false;
		});
		document.documentElement.classList.add("has-mobile-navigation");
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initializeMobileNavigation);
	} else {
		initializeMobileNavigation();
	}
})();
