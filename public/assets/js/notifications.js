console.log('✅ notifications.js loaded!');

/**
 * Notification System using jQuery
 */

// Base URL for API calls (set this from the page)
var baseUrl = '';

console.log('✅ jQuery available?', typeof jQuery !== 'undefined');

$(document).ready(function() {
    console.log('✅ Document ready!');
    
    // Get base URL from a data attribute or meta tag
    baseUrl = $('meta[name="base-url"]').attr('content') || '';
    
    console.log('✅ Base URL:', baseUrl);
    
    // Load notifications when page loads
    console.log('✅ Calling loadNotifications()...');
    loadNotifications();
    
    // Update every 60 seconds instead of 30
    setInterval(loadNotifications, 60000);  // 60000ms = 60 seconds
    
    // Reload notifications when dropdown is opened
    $('#notificationDropdown').on('click', function() {
        console.log('🔔 Bell clicked!');
        loadNotifications();
    });
    
    console.log('✅ Setup complete!');
});

/**
 * Load notifications from server using $.get()
 */
function loadNotifications() {
    console.log('📡 loadNotifications() called');
    console.log('📡 Request URL:', baseUrl + '/notifications');
    
    $.get(baseUrl + '/notifications', function(response) {
        console.log('✅ Response received:', response);
        
        if (response.success) {
            updateBadge(response.unread_count);
            displayNotifications(response.notifications);
        }
    }).fail(function(xhr, status, error) {
        console.error('❌ Error loading notifications:', error);
        console.error('❌ Status:', status);
        console.error('❌ Response:', xhr.responseText);
    });
}

/**
 * Update the notification badge count
 * @param {number} count - Number of unread notifications
 */
function updateBadge(count) {
    const badge = $('#notification-badge');
    
    if (count > 0) {
        badge.text(count > 9 ? '9+' : count);
        badge.show();
    } else {
        badge.hide();
    }
}

/**
 * Display notifications in the dropdown
 * @param {array} notifications - Array of notification objects
 */
function displayNotifications(notifications) {
    const listContainer = $('#notification-list');
    
    // Clear existing notifications
    listContainer.empty();
    
    if (notifications.length === 0) {
        // Show empty state
        listContainer.html(`
            <li>
                <div class="empty-notifications">
                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                    <p class="mb-0 mt-2">No notifications yet</p>
                </div>
            </li>
        `);
        return;
    }
    
    // Display each notification
    notifications.forEach(function(notification) {
        const isUnread = notification.is_read == 0;
        const alertClass = isUnread ? 'alert-info' : 'alert-secondary';
        
        const notificationHtml = `
            <li>
                <div class="alert ${alertClass} notification-item mb-0 rounded-0" data-id="${notification.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="notification-message">
                                ${escapeHtml(notification.message)}
                            </div>
                            <div class="notification-time">
                                <i class="bi bi-clock"></i> ${notification.time_ago}
                            </div>
                        </div>
                        <div class="ms-2">
                            ${isUnread ? '<span class="badge bg-primary">New</span>' : ''}
                            ${isUnread ? `<button class="btn btn-sm btn-outline-primary ms-2 mark-read-btn" data-id="${notification.id}">
                                <i class="bi bi-check"></i> Mark Read
                            </button>` : ''}
                        </div>
                    </div>
                </div>
            </li>
        `;
        
        listContainer.append(notificationHtml);
    });
    
    // Attach click handlers to "Mark as Read" buttons
    $('.mark-read-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const notificationId = $(this).data('id');
        markAsRead(notificationId);
    });
}

/**
 * Mark a notification as read using $.post()
 * @param {number} notificationId - ID of the notification to mark as read
 */
function markAsRead(notificationId) {
    $.post(
        baseUrl + '/notifications/mark_read/' + notificationId,
        {},
        function(response) {
            if (response.success) {
                // Remove the notification from the list
                $(`.notification-item[data-id="${notificationId}"]`).parent().fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if list is empty
                    if ($('#notification-list li').length === 0) {
                        displayNotifications([]);
                    }
                });
                
                // Update badge count
                updateBadge(response.unread_count);
            } else {
                alert('Failed to mark notification as read: ' + response.message);
            }
        },
        'json'
    ).fail(function(xhr, status, error) {
        console.error('Error marking notification as read:', error);
        alert('An error occurred. Please try again.');
    });
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}