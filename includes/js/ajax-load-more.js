


jQuery(document).ready(function($) {
    var $portfolioGallery = $('.portfolio-gallery');
    // Initialize Isotope if not already done
        var $portfolioItems = $portfolioGallery.isotope({
            itemSelector: '.portfolio-item', // Adjust selector if needed
            layoutMode: 'fitRows' // Change layout mode as needed
        });
    var page = 1;
    $('#wapp-load-more').on('click', function(e) {
        var button = $(this);
        var nonce = button.data('nonce'); // Get nonce from button attribute
        page++;
        e.preventDefault();
            var $this = $(this);
            var data = {
                action: 'wapp_load_more',
                page: page,
                nonce: nonce
            };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function(xhr) {
                button.text('Loading...'); // Optional: update button text
            },
            success: function(response) {
                if (response) {
                    $portfolioGallery.append(response); // Append new posts
                    page++; // Increment the page number
                    $this.text('Load More'); // Reset button text

                    // Reinitialize Isotope with a delay to ensure new items are loaded
                    setTimeout(function () {
                        $portfolioItems.isotope('reloadItems').isotope({
                            animationEngine: 'best-available',
                            itemSelector: '.portfolio-item' // Adjust selector if needed
                        });
                    }, 300); // Delay to allow for new items to be rendered
                } else {
                    $this.hide(); // Hide button if no more posts
                }
            }
        });
    });
});
