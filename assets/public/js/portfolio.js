(function ($) {
	"use strict";

	$(document).ready(function () {
	  /* ---------------------------------------------
		 Portfolio Filtering
		 --------------------------------------------- */

	  var $portfolio = $(".portfolio-grid");

	  // Ensure images are loaded before initializing Isotope
	  imagesLoaded($portfolio, function () {
		$portfolio.isotope({
		  itemSelector: ".portfolio-item",
		  filter: "*",
		});
		$(window).trigger("resize");
	  });

	  $(".portfolio-filter").on("click", "a", function (e) {
		e.preventDefault();
		$(this).parent().addClass("active").siblings().removeClass("active");
		var filterValue = $(this).attr("data-filter");
		$portfolio.isotope({ filter: filterValue });
	  });

	  /*-----------------------------------------------------
		 Magnific Popup Init
		 ------------------------------------------------------- */

	  // Uncomment and configure if needed
	  // $(".portfolio-gallery").each(function () {
	  //   $(this).find(".popup-gallery").magnificPopup({
	  //     type: "image",
	  //     gallery: {
	  //       enabled: true,
	  //     },
	  //   });
	  // });

	  /*-----------------------------------------------------
		 Ajax Load More
		 ------------------------------------------------------- */
		 

	});

  })(jQuery);



