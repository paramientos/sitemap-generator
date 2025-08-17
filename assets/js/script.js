/**
 * Modern Sitemap Generator JavaScript
 * Enhanced user experience and interactions
 */

// DOM Elements
const form = document.querySelector('.sitemap-form');
const submitBtn = form?.querySelector('button[type="submit"]');
const baseUrlInput = document.getElementById('base_url');
const sitemapContent = document.getElementById('sitemap-content');

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    initializeTooltips();
    initializeAnimations();
});

/**
 * Initialize form functionality
 */
function initializeForm() {
    if (!form || !submitBtn) return;
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default form submission
        
        const baseUrl = baseUrlInput?.value.trim();
        
        if (!baseUrl) {
            showAlert('Please enter a valid URL!', 'error');
            return;
        }
        
        if (!isValidUrl(baseUrl)) {
            showAlert('Please enter a valid URL format! (e.g. https://example.com)', 'error');
            return;
        }
        
        // Show loading state
        showLoadingState();
        
        // Submit form via AJAX to prevent immediate download
        submitFormAjax(new FormData(form));
    });
    
    // Real-time URL validation
    baseUrlInput?.addEventListener('input', function() {
        validateUrlInput(this);
    });
    
    // Auto-format URL
    baseUrlInput?.addEventListener('blur', function() {
        formatUrl(this);
    });
}

/**
 * Show loading state during form submission
 */
function showLoadingState() {
    if (!submitBtn) return;
    
    const icon = submitBtn.querySelector('i:not(.fa-spin)');
    const spinner = submitBtn.querySelector('.fa-spin');
    
    if (icon) icon.style.display = 'none';
    if (spinner) spinner.style.display = 'inline-block';
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-cog fa-spin"></i> Generating Sitemap...';
}

/**
 * Validate URL input in real-time
 */
function validateUrlInput(input) {
    const url = input.value.trim();
    
    if (!url) {
        input.classList.remove('valid', 'invalid');
        return;
    }
    
    if (isValidUrl(url)) {
        input.classList.remove('invalid');
        input.classList.add('valid');
    } else {
        input.classList.remove('valid');
        input.classList.add('invalid');
    }
}

/**
 * Auto-format URL (add https if missing)
 */
function formatUrl(input) {
    let url = input.value.trim();
    
    if (url && !url.match(/^https?:\/\//)) {
        url = 'https://' + url;
        input.value = url;
        validateUrlInput(input);
    }
}

/**
 * Validate URL format
 */
function isValidUrl(string) {
    try {
        const url = new URL(string);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch (_) {
        return false;
    }
}

/**
 * Copy sitemap content to clipboard
 */
function copySitemap() {
    if (!sitemapContent) {
        showAlert('No sitemap found to copy!', 'error');
        return;
    }
    
    const textContent = sitemapContent.textContent || sitemapContent.innerText;
    
    if (navigator.clipboard && window.isSecureContext) {
        // Modern clipboard API
        navigator.clipboard.writeText(textContent).then(() => {
            showAlert('Sitemap copied to clipboard!', 'success');
            animateButton(event.target);
        }).catch(() => {
            fallbackCopyTextToClipboard(textContent);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(textContent);
    }
}

/**
 * Submit form via AJAX
 */
function submitFormAjax(formData) {
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Parse the response HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Check for error
        const errorDiv = doc.querySelector('.alert-error');
        if (errorDiv) {
            const errorText = errorDiv.textContent.trim();
            showAlert(errorText, 'error');
            hideLoadingState();
            return;
        }
        
        // Check for success result
         const resultDiv = doc.querySelector('.result-section');
         if (resultDiv) {
             // Replace the current page content with the result
             const currentResultDiv = document.querySelector('.result-section');
             if (currentResultDiv) {
                 currentResultDiv.remove();
             }
             
             // Insert the new result after the form section
             const formSection = document.querySelector('.form-section');
             formSection.insertAdjacentHTML('afterend', resultDiv.outerHTML);
             
             // Scroll to result
             setTimeout(() => {
                 const newResultDiv = document.querySelector('.result-section');
                 if (newResultDiv) {
                     newResultDiv.scrollIntoView({ behavior: 'smooth' });
                 }
             }, 100);
         }
        
        hideLoadingState();
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while generating the sitemap.', 'error');
        hideLoadingState();
    });
}

/**
 * Hide loading state
 */
function hideLoadingState() {
    const submitBtn = document.querySelector('.sitemap-form button[type="submit"]');
    const spinner = submitBtn?.querySelector('.fa-spin');
    const icon = submitBtn?.querySelector('.fa-magic');
    
    if (spinner) spinner.style.display = 'none';
    if (icon) icon.style.display = 'inline';
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = submitBtn.innerHTML.replace('Generating...', 'Generate Sitemap');
    }
}

/**
 * Generate new sitemap (scroll to form)
 */
function generateNew() {
    // Remove existing result
    const resultDiv = document.querySelector('.result-section');
    if (resultDiv) {
        resultDiv.remove();
    }
    
    // Scroll to top of the page
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    
    // Focus on the URL input
    setTimeout(() => {
        const urlInput = document.getElementById('base_url');
        if (urlInput) {
            urlInput.focus();
            urlInput.select();
        }
    }, 500);
}

/**
 * Fallback copy method for older browsers
 */
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showAlert('Sitemap copied to clipboard!', 'success');
            animateButton(event.target);
        } else {
            showAlert('Copy operation failed!', 'error');
        }
    } catch (err) {
        showAlert('Copy not supported!', 'error');
    }
    
    document.body.removeChild(textArea);
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-dynamic');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dynamic`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.animation = 'slideInRight 0.3s ease';
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    alertDiv.innerHTML = `
        <i class="fas ${icon}"></i>
        ${message}
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; float: right; cursor: pointer; font-size: 1.2em;">&times;</button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

/**
 * Animate button on click
 */
function animateButton(button) {
    if (!button) return;
    
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 150);
}

/**
 * Initialize tooltips for form elements
 */
function initializeTooltips() {
    const tooltips = {
        'base_url': 'Main URL of the website to generate sitemap for',
        'max_depth': 'How many levels deep to crawl',
        'change_freq': 'How frequently pages are updated',
        'priority': 'Priority level of pages (0.1 - 1.0)'
    };
    
    Object.keys(tooltips).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.title = tooltips[id];
            element.setAttribute('data-tooltip', tooltips[id]);
        }
    });
}

/**
 * Initialize page animations
 */
function initializeAnimations() {
    // Animate form elements on load
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            group.style.transition = 'all 0.5s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Animate result section if exists
    const resultSection = document.querySelector('.result-section');
    if (resultSection) {
        resultSection.style.opacity = '0';
        resultSection.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            resultSection.style.transition = 'all 0.6s ease';
            resultSection.style.opacity = '1';
            resultSection.style.transform = 'translateY(0)';
        }, 300);
    }
}

/**
 * Add CSS animations
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .form-group input.valid {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .form-group input.invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn:active::before {
        width: 300px;
        height: 300px;
    }
`;
document.head.appendChild(style);

/**
 * Handle form validation on page load
 */
window.addEventListener('load', function() {
    // Validate URL if already filled
    if (baseUrlInput?.value) {
        validateUrlInput(baseUrlInput);
    }
    
    // Add smooth scrolling to result section
    const resultSection = document.querySelector('.result-section');
    if (resultSection) {
        resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

/**
 * Keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to submit form
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        if (form && !submitBtn?.disabled) {
            form.requestSubmit();
        }
    }
    
    // Escape to clear form
    if (e.key === 'Escape') {
        const alerts = document.querySelectorAll('.alert-dynamic');
        alerts.forEach(alert => alert.remove());
    }
});

/**
 * Export functions for global access
 */
window.copySitemap = copySitemap;
window.generateNew = generateNew;
window.showAlert = showAlert;
window.animateButton = animateButton;