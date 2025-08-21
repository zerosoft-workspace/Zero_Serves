// public/js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin panel
    AdminPanel.init();
});

const AdminPanel = {
    // Initialize all components
    init() {
        this.initSidebar();
        this.initDropdowns();
        this.initResponsive();
        this.setActiveMenuItem();
    },

    // Sidebar functionality
    initSidebar() {
        const toggle = document.querySelector('.navbar-toggle');
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const main = document.querySelector('.admin-main');

        if (!toggle || !sidebar) return;

        // Create overlay if it doesn't exist
        if (!overlay) {
            const overlayDiv = document.createElement('div');
            overlayDiv.className = 'sidebar-overlay';
            document.body.appendChild(overlayDiv);
        }

        // Toggle sidebar on mobile
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleSidebar();
        });

        // Close sidebar when clicking overlay
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('sidebar-overlay')) {
                this.closeSidebar();
            }
        });

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeSidebar();
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    },

    // Toggle sidebar visibility
    toggleSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const body = document.body;

        if (sidebar.classList.contains('show')) {
            this.closeSidebar();
        } else {
            sidebar.classList.add('show');
            if (overlay) overlay.classList.add('show');
            body.style.overflow = 'hidden';
        }
    },

    // Close sidebar
    closeSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const body = document.body;

        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        body.style.overflow = '';
    },

    // Handle window resize
    handleResize() {
        if (window.innerWidth > 768) {
            this.closeSidebar();
        }
    },

    // Initialize dropdown menus
    initDropdowns() {
        const dropdowns = document.querySelectorAll('.user-dropdown');

        dropdowns.forEach(dropdown => {
            const trigger = dropdown.querySelector('.user-info');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (!trigger || !menu) return;

            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Close other dropdowns
                this.closeAllDropdowns();
                
                // Toggle current dropdown
                menu.classList.toggle('show');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            this.closeAllDropdowns();
        });

        // Close dropdowns on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllDropdowns();
            }
        });
    },

    // Close all dropdown menus
    closeAllDropdowns() {
        const dropdownMenus = document.querySelectorAll('.dropdown-menu');
        dropdownMenus.forEach(menu => {
            menu.classList.remove('show');
        });
    },

    // Set active menu item based on current URL
    setActiveMenuItem() {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.sidebar-menu a');

        menuItems.forEach(item => {
            item.classList.remove('active');
            
            const href = item.getAttribute('href');
            if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
                item.classList.add('active');
            }
        });
    },

    // Initialize responsive behavior
    initResponsive() {
        // Handle touch events for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        });
    },

    // Handle swipe gestures
    handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;

        // Swipe right to open sidebar
        if (swipeDistance > swipeThreshold && touchStartX < 50) {
            if (window.innerWidth <= 768) {
                this.toggleSidebar();
            }
        }
        
        // Swipe left to close sidebar
        if (swipeDistance < -swipeThreshold && touchStartX < 300) {
            if (window.innerWidth <= 768) {
                this.closeSidebar();
            }
        }
    },

    // Utility functions
    utils: {
        // Show notification
        showNotification(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span>${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, duration);

            // Manual close
            const closeBtn = notification.querySelector('.notification-close');
            closeBtn.addEventListener('click', () => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            });
        },

        // Loading state management
        setLoading(element, loading = true) {
            if (!element) return;

            if (loading) {
                element.disabled = true;
                element.innerHTML = `
                    <i class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></i>
                    Yükleniyor...
                `;
            } else {
                element.disabled = false;
                // Restore original content - should be handled by specific implementation
            }
        },

        // Format numbers
        formatNumber(number) {
            return new Intl.NumberFormat('tr-TR').format(number);
        },

        // Format currency
        formatCurrency(amount) {
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY'
            }).format(amount);
        }
    }
};

// Additional utility functions that can be used across admin pages
window.AdminUtils = AdminPanel.utils;