document.addEventListener('DOMContentLoaded', function() {
    // Tooltip initialization (Bootstrap)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Favorite recipe functionality
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const recipeId = this.getAttribute('data-recipe-id');
            const isFavorite = this.classList.contains('active');
            
            // If user is not logged in, redirect to login
            if (!isUserLoggedIn()) {
                window.location.href = '/blog-nau-an/login.php';
                return;
            }
            
            // Toggle favorite status
            toggleFavorite(recipeId, !isFavorite);
            
            // Update UI
            this.classList.toggle('active');
            const iconElement = this.querySelector('i');
            
            if (this.classList.contains('active')) {
                iconElement.classList.remove('far');
                iconElement.classList.add('fas');
                this.setAttribute('title', 'Xóa khỏi yêu thích');
            } else {
                iconElement.classList.remove('fas');
                iconElement.classList.add('far');
                this.setAttribute('title', 'Thêm vào yêu thích');
            }
            
            // Reinitialize tooltip
            const tooltip = bootstrap.Tooltip.getInstance(this);
            if (tooltip) {
                tooltip.dispose();
            }
            new bootstrap.Tooltip(this);
        });
    });
    
    // Check if user is logged in
    function isUserLoggedIn() {
        // This would typically check a session variable
        // For client-side, we'll just check if the user-menu element exists
        return document.getElementById('user-menu') !== null;
    }
    
    // Toggle favorite status via AJAX
    function toggleFavorite(recipeId, addToFavorite) {
        fetch('/blog-nau-an/includes/ajax/toggle-favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `recipe_id=${recipeId}&add=${addToFavorite ? 1 : 0}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show a toast or notification
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Đã xảy ra lỗi', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Đã xảy ra lỗi khi xử lý yêu cầu', 'danger');
        });
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        // Remove toast from DOM after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
    
    // Search form validation
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = document.getElementById('search-input');
            if (!searchInput.value.trim()) {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }
    
    // Comment form validation
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            const commentContent = document.getElementById('comment-content');
            if (!commentContent.value.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập nội dung bình luận');
                commentContent.focus();
            }
        });
    }
});
