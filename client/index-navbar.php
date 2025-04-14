<style>
    #notificationBadge {
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 2px 4px;
        font-size: 12px;
        position: absolute;
        top: 10px;
        right: 10px;
        display: none; /* Hidden by default */
    }
    
    #notificationPulse {
        display: none; /* Hidden by default */
    }
    
    .dropdown-body {
        max-height: 300px;
        overflow-y: auto;
    }
</style>

<nav class="navbar navbar-expand-lg">
      <a class="navbar-brand" href="index.php"><img src="../images/logo.jfif" alt="logo"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#headerMenu"
        aria-controls="headerMenu" aria-expanded="false" aria-label="Toggle navigation">
        <i class="icon ion-md-menu"></i>
      </button>

      <div class="collapse navbar-collapse" id="headerMenu">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="icon ion-md-swap"></i> Trade</a>
                </li>
                <!--<li class="nav-item">-->
                <!--    <a class="nav-link" href="./markets/index.php"><i class="icon ion-md-stats"></i> Markets</a>-->
                <!--</li>-->
                <li class="nav-item">
                    <a class="nav-link" href="./profile/index.php"><i class="icon ion-md-person"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./wallet/index.php"><i class="icon ion-md-wallet"></i> Wallet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./settings/index.php"><i class="icon ion-md-settings"></i> Settings</a>
                </li>
            </ul>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item header-custom-icon">
            <a class="nav-link" href="#" id="clickFullscreen">
              <i class="icon ion-md-expand"></i>
            </a>
          </li>
          <li class="nav-item dropdown header-custom-icon">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="notificationDropdown">
                    <i class="icon ion-md-notifications"></i>
                    <span class="circle-pulse" id="notificationPulse"></span>
                </a>
                <div class="dropdown-menu" id="notificationMenu">
                    <div class="dropdown-header d-flex align-items-center justify-content-between">
                        <p class="mb-0 font-weight-medium" id="notificationHeader">0 New Notifications</p>
                    </div>
                    <div class="dropdown-body" id="notificationBody">
                        <!-- Notifications will be dynamically loaded here -->
                    </div>
                    <div class="dropdown-footer text-center" id="notificationFooter" style="display: none;">
                        <a href="#" class="text-muted" id="clearNotifications">Clear all</a>
                    </div>
                </div>
            </li>
          <li class="nav-item dropdown header-img-icon">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">
              <!-- Display the avatar -->
              <img src="<?php echo $avatarPath; ?>" alt="avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
            </a>
            <div class="dropdown-menu">
              <div class="dropdown-header d-flex flex-column align-items-center">
                <div class="figure mb-3">
                <img src="<?php echo $avatarPath; ?>" alt="avatar" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                </div>
                <div class="info text-center">
                  <p class="name font-weight-bold mb-0"><?php echo $userDetails['name']?></p>
                  <p class="email text-muted mb-3"><?php echo $userDetails['email']?></p>
                </div>
              </div>
              <div class="dropdown-body">
                <ul class="profile-nav">
                  <li class="nav-item">
                    <a href="profile" class="nav-link">
                      <i class="icon ion-md-person"></i>
                      <span>Profile</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="wallet" class="nav-link">
                      <i class="icon ion-md-wallet"></i>
                      <span>My Wallet</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="settings" class="nav-link">
                      <i class="icon ion-md-settings"></i>
                      <span>Settings</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="../model/exit.php" class="nav-link red">
                      <i class="icon ion-md-power"></i>
                      <span>Log Out</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    
    <script>
        // Function to fetch notifications
        function fetchNotifications() {
            fetch('../model/fetch_notifications.php')
                .then(response => response.json())
                .then(data => {
                    const notificationBody = document.getElementById('notificationBody');
                    const notificationHeader = document.getElementById('notificationHeader');
                    const notificationPulse = document.getElementById('notificationPulse');
                    const notificationFooter = document.getElementById('notificationFooter');
    
                    // Clear the notification body
                    notificationBody.innerHTML = '';
    
                    if (data.notifications && data.notifications.length > 0) {
                        // Count unseen notifications
                        const unseenCount = data.notifications.filter(notification => notification.seen === 0).length;
    
                        if (unseenCount > 0) {
                            notificationHeader.textContent = `${unseenCount} New Notification${unseenCount > 1 ? 's' : ''}`;
                            notificationPulse.style.display = 'block'; // Show notification pulse
                        } else {
                            notificationHeader.textContent = 'Notifications Available';
                            notificationPulse.style.display = 'none'; // Hide notification pulse
                        }
    
                        notificationFooter.style.display = 'block'; // Show "Clear All" button
    
                        // Populate notifications
                        data.notifications.forEach(notification => {
                            const notificationItem = document.createElement('a');
                            notificationItem.href = '#';
                            notificationItem.className = 'dropdown-item';
                            notificationItem.innerHTML = `
                                <div class="icon">
                                    <i class="icon ion-md-notifications"></i>
                                </div>
                                <div class="content">
                                    <p>${notification.message}</p>
                                    <p class="sub-text text-muted">${notification.created_at}</p>
                                </div>`;
                            notificationItem.addEventListener('click', () => markNotificationSeen(notification.id));
                            notificationBody.appendChild(notificationItem);
                        });
                    } else {
                        // No notifications
                        notificationHeader.textContent = 'No Notifications';
                        notificationPulse.style.display = 'none'; // Hide notification pulse
                        notificationFooter.style.display = 'none'; // Hide "Clear All" button
                        notificationBody.innerHTML = '<p class="text-center text-muted">No notifications available</p>';
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }
    
        // Mark notification as seen
        function markNotificationSeen(notificationId) {
            fetch(`../model/mark_notification_seen.php?id=${notificationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNotifications(); // Refresh notifications
                    } else {
                        console.error('Error marking notification as seen:', data.error);
                    }
                })
                .catch(error => console.error('Error marking notification as seen:', error));
        }
    
        // Function to clear all notifications
        document.getElementById('clearNotifications').addEventListener('click', (e) => {
            e.preventDefault();
    
            fetch('../model/clear_notifications.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNotifications(); // Refresh notifications
                    } else {
                        console.error('Error clearing notifications:', data.error);
                    }
                })
                .catch(error => console.error('Error clearing notifications:', error));
        });
    
        // Initial fetch
        fetchNotifications();
    
        // Refresh notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
    </script>