// K.N. Raam Hardware - Enhanced JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initAccessibility();
    initNotifications();
    initSearchEnhancement();
    initFormValidation();
    initSocialSharing();
    initCartEnhancement();
    initDropdowns();
});

// ==================== ACCESSIBILITY FEATURES ====================

function initAccessibility() {
    // Add skip to main content link
    addSkipLink();
    
    // Add ARIA labels and roles
    enhanceARIA();
    
    // Keyboard navigation
    initKeyboardNavigation();
    
    // Focus management
    initFocusManagement();
}

function addSkipLink() {
    const skipLink = document.createElement('a');
    skipLink.href = '#main-content';
    skipLink.className = 'skip-link';
    skipLink.textContent = 'Skip to main content';
    document.body.insertBefore(skipLink, document.body.firstChild);
    
    // Add main content ID if not exists
    const mainContent = document.querySelector('main') || document.querySelector('.container');
    if (mainContent && !mainContent.id) {
        mainContent.id = 'main-content';
    }
}

function enhanceARIA() {
    // Add ARIA labels to search form
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && !searchInput.getAttribute('aria-label')) {
        searchInput.setAttribute('aria-label', 'Search products by name or description');
    }
    
    // Add ARIA labels to quantity inputs
    const qtyInputs = document.querySelectorAll('input[name="qty"]');
    qtyInputs.forEach(input => {
        if (!input.getAttribute('aria-label')) {
            input.setAttribute('aria-label', 'Quantity');
        }
    });
    
    // Add ARIA labels to buttons
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        if (!button.getAttribute('aria-label') && !button.textContent.trim()) {
            const icon = button.querySelector('i');
            if (icon) {
                const iconClass = icon.className;
                if (iconClass.includes('search')) {
                    button.setAttribute('aria-label', 'Search');
                } else if (iconClass.includes('cart')) {
                    button.setAttribute('aria-label', 'Add to cart');
                } else if (iconClass.includes('facebook')) {
                    button.setAttribute('aria-label', 'Share on Facebook');
                } else if (iconClass.includes('twitter')) {
                    button.setAttribute('aria-label', 'Share on Twitter');
                } else if (iconClass.includes('whatsapp')) {
                    button.setAttribute('aria-label', 'Share on WhatsApp');
                }
            }
        }
    });
}

function initKeyboardNavigation() {
    // Handle Enter key on search form
    const searchForm = document.querySelector('form[action*="search"]');
    if (searchForm) {
        searchForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.submit();
            }
        });
    }
    
    // Handle keyboard navigation for product cards
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = this.querySelector('a');
                if (link) {
                    link.click();
                }
            }
        });
    });
}

function initFocusManagement() {
    // Focus first form input when modal opens
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const firstInput = this.querySelector('input, textarea, select');
            if (firstInput) {
                firstInput.focus();
            }
        });
    });
}

// ==================== NOTIFICATION SYSTEM ====================

function initNotifications() {
    // Create notification container
    const notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    notificationContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 350px;';
    document.body.appendChild(notificationContainer);
}

function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `notification ${type} alert alert-${type === 'error' ? 'danger' : type}`;
    notification.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div>${message}</div>
            <button type="button" class="btn-close" aria-label="Close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, duration);
    
    // Announce to screen readers
    announceToScreenReader(message);
}

function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = message;
    document.body.appendChild(announcement);
    
    setTimeout(() => {
        announcement.remove();
    }, 1000);
}

// ==================== SEARCH ENHANCEMENT ====================

function initSearchEnhancement() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;
    
    // Add search suggestions
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2) {
                fetchSearchSuggestions(this.value);
            }
        }, 300);
    });
    
    // Add search history
    loadSearchHistory();
}

function fetchSearchSuggestions(query) {
    // This would typically make an AJAX call to get suggestions
    // For now, we'll just show a placeholder
    console.log('Fetching suggestions for:', query);
}

function loadSearchHistory() {
    const searchHistory = localStorage.getItem('searchHistory');
    if (searchHistory) {
        const history = JSON.parse(searchHistory);
        // Could display search history dropdown
    }
}

function saveSearchQuery(query) {
    let history = JSON.parse(localStorage.getItem('searchHistory') || '[]');
    if (!history.includes(query)) {
        history.unshift(query);
        history = history.slice(0, 10); // Keep only last 10 searches
        localStorage.setItem('searchHistory', JSON.stringify(history));
    }
}

// ==================== FORM VALIDATION ====================

function initFormValidation() {
    // Enhanced form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
    
    // Real-time validation
    const inputs = document.querySelectorAll('input[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Remove existing error styling
    field.classList.remove('is-invalid');
    const existingError = field.parentElement.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
    
    // Validation rules
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required.';
    } else if (field.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid email address.';
    } else if (field.type === 'number' && value && field.min && parseInt(value) < parseInt(field.min)) {
        isValid = false;
        errorMessage = `Minimum value is ${field.min}.`;
    } else if (field.type === 'number' && value && field.max && parseInt(value) > parseInt(field.max)) {
        isValid = false;
        errorMessage = `Maximum value is ${field.max}.`;
    }
    
    if (!isValid) {
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = errorMessage;
        field.parentElement.appendChild(errorDiv);
    }
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// ==================== SOCIAL SHARING ====================

function initSocialSharing() {
    // Enhanced social sharing with analytics
    const shareButtons = document.querySelectorAll('[href*="facebook"], [href*="twitter"], [href*="whatsapp"]');
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            trackSocialShare(this.href);
        });
    });
}

function trackSocialShare(shareUrl) {
    // Track social sharing (could send to analytics service)
    console.log('Social share:', shareUrl);
    
    // Show success notification
    showNotification('Shared successfully!', 'success', 3000);
}

// ==================== CART ENHANCEMENT ====================

function initCartEnhancement() {
    // Add to cart with AJAX (if needed)
    const addToCartForms = document.querySelectorAll('form[action*="products.php"]');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Could implement AJAX cart addition here
            showNotification('Adding to cart...', 'info', 2000);
        });
    });
    
    // Quantity input enhancement
    const qtyInputs = document.querySelectorAll('input[name="qty"]');
    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateTotalPrice(this);
        });
    });
}

function updateTotalPrice(qtyInput) {
    const card = qtyInput.closest('.card');
    if (!card) return;
    
    const priceElement = card.querySelector('.product-price');
    if (!priceElement) return;
    
    const price = parseFloat(priceElement.textContent.replace('LKR ', '').replace(',', ''));
    const qty = parseInt(qtyInput.value) || 0;
    const total = price * qty;
    
    // Could update a total display element here
    console.log('Total price:', total);
}

// ==================== UTILITY FUNCTIONS ====================

// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textArea);
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for performance
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ==================== DROPDOWN INITIALIZATION ====================

function initDropdowns() {
    // Initialize Bootstrap dropdowns
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            try {
                const dropdown = new bootstrap.Dropdown(toggle);
                console.log('Dropdown initialized for:', toggle.id);
                
                // Add click event for debugging
                toggle.addEventListener('click', function(e) {
                    console.log('Dropdown clicked:', this.id);
                });
            } catch (error) {
                console.error('Error initializing dropdown:', error);
            }
        } else {
            console.warn('Bootstrap not available for dropdown initialization');
        }
    });
    
    // Alternative: Use data attributes for automatic initialization
    const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdownElements.forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdownMenu.classList.toggle('show');
                this.setAttribute('aria-expanded', dropdownMenu.classList.contains('show'));
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
                const toggle = dropdown.previousElementSibling;
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
}

// ==================== GLOBAL FUNCTIONS ====================

// Make functions globally available
window.showNotification = showNotification;
window.copyToClipboard = copyToClipboard;
window.validateForm = validateForm;
window.validateField = validateField;
