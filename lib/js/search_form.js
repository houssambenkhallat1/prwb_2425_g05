$(document).ready(function() {
    // Configuration
    const config = {
        minChars: 0, // Allow empty search to show color filters only
        debounceDelay: 300
    };

    // Cache DOM elements
    const $searchInput = $('#search-input, input[name="search"]');
    const $colorFilters = $('.color-filter');
    const $formsContainer = $('#main-container .row');
    const $noResultsMessage = $('#no-results-message');
    const $searchForm = $('#search-form');
    let searchTimer;

    // Hide PHP form when JS is enabled
    $searchForm.hide();

    // Prepend dynamic search elements
    $('#main-container').prepend(`
        <div class="mb-4">
            <div class="input-group mb-3">
                <span class="input-group-text" id="search-forms"><i class="bi bi-search"></i></span>
                <input id="search-input" type="text" class="form-control" placeholder="Search forms..."
                       aria-label="search-forms" aria-describedby="search-forms">
            </div>
            <div class="mb-3">
                <label class="form-label">Filter by colors:</label>
                <div class="d-flex flex-wrap gap-2" id="color-filters">
                    <label class="color-filter" data-color="blue">
                        <span class="color-badge bg-primary" title="Blue"></span>
                    </label>
                    <label class="color-filter" data-color="red">
                        <span class="color-badge bg-danger" title="Red"></span>
                    </label>
                    <label class="color-filter" data-color="green">
                        <span class="color-badge bg-success" title="Green"></span>
                    </label>
                    <label class="color-filter" data-color="yellow">
                        <span class="color-badge bg-warning" title="Yellow"></span>
                    </label>
                </div>
            </div>
        </div>
    `);

    // Update DOM cache after adding elements
    const $dynamicSearchInput = $('#search-input');
    const $dynamicColorFilters = $('#color-filters .color-filter');

    // Search input handler
    $dynamicSearchInput.on('input', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val().trim();

        searchTimer = setTimeout(() => {
            performSearch();
        }, config.debounceDelay);
    });

    // Color filter handlers
    $dynamicColorFilters.on('click', function() {
        $(this).toggleClass('active');
        performSearch();
    });

    // Decode search key on page load
    decodeSearchKey();

    function performSearch() {
        const searchTerm = $dynamicSearchInput.val().trim();
        const selectedColors = [];

        $dynamicColorFilters.filter('.active').each(function() {
            selectedColors.push($(this).data('color'));
        });

        $.ajax({
            url: 'form/search_json_forms',
            method: 'POST',
            data: {
                search_key: searchTerm,
                colors: selectedColors
            },
            dataType: 'json',
            success: function(response) {
                // Hide all forms initially
                $formsContainer.find('.form-container').hide();

                let visibleCount = 0;

                if (searchTerm === '' && selectedColors.length === 0) {
                    // Show all forms if no filters
                    $formsContainer.find('.form-container').show();
                    visibleCount = $formsContainer.find('.form-container').length;
                } else {
                    // Show matching forms from server response
                    response.allowed_forms_ids.forEach(function(formId) {
                        const $form = $formsContainer.find(`.form-container[data-form-id="${formId}"]`);
                        if ($form.length) {
                            $form.show();
                            visibleCount++;
                        }
                    });
                }

                // Update propagation links with encoded search data
                if (response.encoded_search_key) {
                    updatePropagationLinks(response.encoded_search_key);
                }

                // Update no results message
                if (visibleCount > 0) {
                    $noResultsMessage.addClass('d-none');
                } else {
                    $noResultsMessage.removeClass('d-none');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching search results:', error);
                $noResultsMessage.removeClass('d-none').text('Error searching forms. Please try again.');
            }
        });
    }

    function decodeSearchKey() {
        const currentPath = window.location.pathname;
        const pathParts = currentPath.split('/form/list/');

        if (pathParts.length > 1 && pathParts[1]) {
            const encodedSearchKey = pathParts[1].split('/')[0];

            $.post("form/decode_search_key", {encoded_search_key: encodedSearchKey})
                .done(function(response) {
                    if (response.decoded_search_key) {
                        $dynamicSearchInput.val(response.decoded_search_key);
                    }
                    if (response.decoded_colors && response.decoded_colors.length > 0) {
                        response.decoded_colors.forEach(function(color) {
                            $dynamicColorFilters.filter(`[data-color="${color}"]`).addClass('active');
                        });
                    }
                    // Trigger search with decoded values
                    performSearch();
                })
                .fail(function() {
                    console.error('Failed to decode search key');
                });
        }
    }

    function updatePropagationLinks(encodedKey) {
        $('.search-propagation').each(function() {
            const $link = $(this);

            if (!$link.data('original-href')) {
                $link.data('original-href', $link.attr('href'));
            }

            const originalHref = $link.data('original-href');
            let updatedHref = originalHref.replace(/\/+$/, '');
            updatedHref += '/' + encodedKey;

            $link.attr('href', updatedHref);
        });
    }

    function resetPropagationLinks() {
        $('.search-propagation').each(function() {
            const $link = $(this);
            const originalHref = $link.data('original-href');

            if (originalHref) {
                $link.attr('href', originalHref);
            }
        });
    }
});