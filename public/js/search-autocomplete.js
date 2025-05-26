/**
 * Search Autocomplete Functionality
 * 
 * Provides real-time search suggestions for products with keyboard navigation
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const suggestionsContainer = document.getElementById('search-suggestions');
    let selectedIndex = -1;
    let suggestions = [];
    
    if (!searchInput || !suggestionsContainer) return;
    
    // Add event listeners
    searchInput.addEventListener('input', debounce(handleSearchInput, 300));
    searchInput.addEventListener('keydown', handleKeyNavigation);
    document.addEventListener('click', handleClickOutside);
    
    /**
     * Debounce function to limit API calls during typing
     */
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }
    
    /**
     * Handle search input changes
     */
    function handleSearchInput() {
        const query = searchInput.value.trim();
        
        // Clear suggestions if query is too short
        if (query.length < 2) {
            clearSuggestions();
            return;
        }
        
        // Fetch suggestions from API
        fetch(`../ajax/search_autocomplete.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                suggestions = data.suggestions || [];
                displaySuggestions(suggestions);
            })
            .catch(error => {
                console.error('Error fetching search suggestions:', error);
            });
    }
    
    /**
     * Display search suggestions
     */
    function displaySuggestions(items) {
        // Clear previous suggestions
        clearSuggestions();
        
        if (items.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        // Create suggestion elements
        items.forEach((item, index) => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'search-suggestion-item';
            suggestionItem.innerHTML = `
                <div class="suggestion-content">
                    ${item.image ? `<img src="../public/uploads/products/${item.image}" alt="${item.name}" class="suggestion-image">` : ''}
                    <div class="suggestion-details">
                        <div class="suggestion-name">${item.highlight || item.name}</div>
                        <div class="suggestion-category">${item.category}</div>
                        <div class="suggestion-price">${formatCurrency(item.price)}</div>
                    </div>
                </div>
            `;
            
            // Add click handler
            suggestionItem.addEventListener('click', () => {
                window.location.href = `product_details.php?id=${item.id}`;
            });
            
            // Add hover handler
            suggestionItem.addEventListener('mouseenter', () => {
                selectedIndex = index;
                highlightSuggestion();
            });
            
            suggestionsContainer.appendChild(suggestionItem);
        });
        
        // Show suggestions container
        suggestionsContainer.style.display = 'block';
    }
    
    /**
     * Clear all suggestions
     */
    function clearSuggestions() {
        suggestionsContainer.innerHTML = '';
        suggestionsContainer.style.display = 'none';
        selectedIndex = -1;
    }
    
    /**
     * Handle keyboard navigation
     */
    function handleKeyNavigation(e) {
        const items = suggestionsContainer.querySelectorAll('.search-suggestion-item');
        
        // If no suggestions or container is hidden, skip
        if (items.length === 0 || suggestionsContainer.style.display === 'none') {
            return;
        }
        
        // Arrow Down
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            highlightSuggestion();
        }
        // Arrow Up
        else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = selectedIndex <= 0 ? items.length - 1 : selectedIndex - 1;
            highlightSuggestion();
        }
        // Enter
        else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            items[selectedIndex].click();
        }
        // Escape
        else if (e.key === 'Escape') {
            clearSuggestions();
        }
    }
    
    /**
     * Highlight the currently selected suggestion
     */
    function highlightSuggestion() {
        const items = suggestionsContainer.querySelectorAll('.search-suggestion-item');
        
        // Remove highlighting from all items
        items.forEach(item => {
            item.classList.remove('selected');
        });
        
        // Add highlighting to selected item
        if (selectedIndex >= 0 && selectedIndex < items.length) {
            items[selectedIndex].classList.add('selected');
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    /**
     * Handle clicks outside the search component
     */
    function handleClickOutside(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            clearSuggestions();
        }
    }
    
    /**
     * Format price as currency
     */
    function formatCurrency(price) {
        return 'K' + parseFloat(price).toFixed(2);
    }
    
    /**
     * Price range slider functionality
     */
    const minPriceSlider = document.getElementById('price-slider-min');
    const maxPriceSlider = document.getElementById('price-slider-max');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    
    if (minPriceSlider && maxPriceSlider && minPriceInput && maxPriceInput) {
        // Update input when slider changes
        minPriceSlider.addEventListener('input', function() {
            minPriceInput.value = this.value;
            // Ensure min doesn't exceed max
            if (parseInt(this.value) > parseInt(maxPriceSlider.value)) {
                maxPriceSlider.value = this.value;
                maxPriceInput.value = this.value;
            }
        });
        
        maxPriceSlider.addEventListener('input', function() {
            maxPriceInput.value = this.value;
            // Ensure max doesn't go below min
            if (parseInt(this.value) < parseInt(minPriceSlider.value)) {
                minPriceSlider.value = this.value;
                minPriceInput.value = this.value;
            }
        });
        
        // Update slider when input changes
        minPriceInput.addEventListener('input', function() {
            minPriceSlider.value = this.value;
            // Ensure min doesn't exceed max
            if (parseInt(this.value) > parseInt(maxPriceInput.value)) {
                maxPriceInput.value = this.value;
                maxPriceSlider.value = this.value;
            }
        });
        
        maxPriceInput.addEventListener('input', function() {
            maxPriceSlider.value = this.value;
            // Ensure max doesn't go below min
            if (parseInt(this.value) < parseInt(minPriceInput.value)) {
                minPriceInput.value = this.value;
                minPriceSlider.value = this.value;
            }
        });
    }
});
