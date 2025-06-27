<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ** Đảm bảo file config.php được include để có kết nối $conn **
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php'; // Đảm bảo functions.php được include

// Xử lý yêu cầu AJAX cho thông báo
// Phần này chạy trước khi HTML được gửi đi
if (isset($_GET['action']) && isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Lỗi không xác định.'];
    $user_id = $_SESSION['user_id'];

    if ($_GET['action'] === 'getUnreadCount') {
        $stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $response = ['success' => true, 'count' => $row['unread_count']];
        $stmt->close();
    } elseif ($_GET['action'] === 'getNotifications') {
        // Lấy 10 thông báo gần nhất, ưu tiên chưa đọc lên đầu nếu cần (có thể điều chỉnh ORDER BY)
        $stmt = $conn->prepare("SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY is_read ASC, created_at DESC LIMIT 10"); 
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => $row['id'],
                'message' => htmlspecialchars($row['message']),
                'link' => htmlspecialchars($row['link'] ?? '#'), // Đảm bảo link không null
                'is_read' => (bool)$row['is_read'],
                'created_at' => date('H:i d/m/Y', strtotime($row['created_at'])) // Định dạng ngày tháng kèm giờ
            ];
        }
        $response = ['success' => true, 'notifications' => $notifications];
        $stmt->close();
    } elseif ($_GET['action'] === 'markAsRead' && isset($_POST['notification_id'])) {
        $notification_id = (int)$_POST['notification_id'];
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ? AND is_read = 0"); // Chỉ cập nhật nếu chưa đọc
        $stmt->bind_param("ii", $notification_id, $user_id);
        if ($stmt->execute()) {
            // Kiểm tra xem có dòng nào được cập nhật không
            if ($stmt->affected_rows > 0) {
                 $response = ['success' => true, 'message' => 'Đã đánh dấu là đã đọc.'];
            } else {
                 $response = ['success' => true, 'message' => 'Thông báo đã được đọc hoặc không tìm thấy.']; // Trường hợp đã đọc trước đó
            }
        } else {
            $response = ['success' => false, 'message' => 'Không thể đánh dấu đã đọc: ' . $conn->error]; // Thêm thông báo lỗi CSDL
        }
        $stmt->close();
    }
    
    echo json_encode($response);
    $conn->close();
    exit(); // Rất quan trọng để dừng việc render HTML sau khi gửi JSON
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Bếp Anh Tài'; ?></title>
    
    <?php 
    // Định nghĩa BASE_PATH: Đây là đường dẫn thư mục gốc của dự án của bạn trên server web.
    // Ví dụ: Nếu bạn truy cập http://localhost/blogwebsite/, thì BASE_PATH là '/blogwebsite'.
    // Nếu bạn truy cập trực tiếp http://yourdomain.com/, thì BASE_PATH là '/'.
    // **ĐẢM BẢO GIÁ TRỊ NÀY CHÍNH XÁC VỚI CẤU HÌNH CỦA BẠN.**
    // LƯU Ý: Nếu trang web của bạn chạy trực tiếp từ thư mục gốc của domain (ví dụ: localhost/), thì BASE_PATH nên là '/'.
    // Nếu nó nằm trong một thư mục con (ví dụ: localhost/myblog/), thì BASE_PATH là '/myblog'.
    if (!defined('BASE_PATH')) { 
        define('BASE_PATH', '/blogwebsite'); // <--- KIỂM TRA LẠI ĐƯỜNG DẪN NÀY CHO CHÍNH XÁC
    }
    ?>
    <base href="<?php echo BASE_PATH; ?>/"> 
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,wght@0,400;0,700;1,400&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/header-footer.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <?php if (isset($extraCss)) : ?>
        <link rel="stylesheet" href="<?php echo $extraCss; ?>">
    <?php endif; ?>

    <?php if (isset($extraHeadContent)) : ?>
        <?php echo $extraHeadContent; ?>
    <?php endif; ?>
</head>
<body>

    <div class="background-blur"></div>
    
    <header>
        <div class="header-left">
            <a href="index.php">
            <img src="images/logo.png" alt="Bếp Anh Tài Logo" class="logo-img">
            </a>
        </div>
        <nav class="header-nav">
            <a href="index.php">Trang chủ</a>
            <div class="dropdown-menu-parent">
                <a href="monan/monan.php" class="dropdown-toggle">Món Ăn <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                <div class="dropdown-content">
                    <a href="monan/man.php">Món ăn mặn</a>
                    <a href="monan/chay.php">Món ăn chay</a>
                    <a href="monan/anvat.php">Món ăn vặt</a>
                </div>
            </div>
            <a href="monan/monnuoc.php">Món Nước</a>
            <a href="ve-chung-toi.php">Về Chúng Tôi</a>
        </nav>
        <div class="header-right">
            <form class="search-box" action="index.php" method="GET">
                <input type="text" name="q" placeholder="Tìm kiếm bài viết..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
            </form>
            <div class="icons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="favorite-wrapper-header">
                        <a href="yeuthich.php" class="header-favorite">
                            <i class="fa-regular fa-heart"></i>
                            <span id="favoriteCount" class="favorite-count" style="display:none;">0</span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="favorite-wrapper-header">
                        <a href="#" class="header-favorite disabled">
                            <i class="fa-regular fa-heart"></i>
                            <span class="favorite-count" style="display:none;">0</span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="notification-wrapper">
                        <a href="#" class="notification-button" aria-label="Thông báo">
                            <i class="fa-regular fa-bell"></i>
                            <span class="notification-count" id="notificationCount"></span> 
                        </a>

                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">Thông báo</div>
                            <ul class="notification-list" id="notificationList">
                                <li class="loading-notifications">Đang tải thông báo...</li>
                            </ul>
                            <div class="notification-footer">
                                <a href="#">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="#" class="notification-icon disabled" aria-label="Thông báo">
                        <i class="fa-regular fa-bell"></i>
                    </a>
                <?php endif; ?>        
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-dropdown">
                <button class="login-button user-toggle">
                    <i class="fa-regular fa-user"></i> <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <i class="fas fa-right-from-bracket"></i> </button>
                <div class="user-menu">
                    <a href="logout.php" class="logout-link">Đăng xuất</a>
                </div>
            </div>
            <?php else: ?>
                <a href="dangnhap.php" class="login-button"><i class="fa-regular fa-user"></i> Đăng Nhập / Đăng Ký</a>
            <?php endif; ?>
    </header>

    <main class="page-content">
        <?php 
            // Đây là nơi nội dung của từng trang cụ thể sẽ được chèn vào
            // Biến $contentPage sẽ chứa đường dẫn đến file nội dung (ví dụ: 'content/trang-chu-content.php')
            if (isset($contentPage) && file_exists($contentPage)) {
                include $contentPage; 
            } else {
                echo "<p>Nội dung trang không tìm thấy.</p>";
            }
        ?> 
    </main>

    <footer>
        <div class="footer-section">
            <div class="footer-logo">Bếp Anh Tài</div>
            <p class="footer-slogan">
                Chỉ cần một chút yêu thương, ai cũng có thể nấu được những món ăn ngon cho gia đình!
            </p>
        </div>

        <div class="footer-section">
            <h4>Thông tin</h4>
            <p>Hộ Kinh Doanh Bếp Anh Tài</p>
            <p>Mã Số HKD: 26A8046871</p>
            <p>Địa chỉ: Trường Đại học Giao thông vận tải, TPHCM - 70 Tô Ký, Tân Chánh Hiệp, Quận 12</p>
        </div>

        <div class="footer-section">
            <h4>Hỗ trợ</h4>
            <p>Hotline: 0348 462 142</p>
            <p>Email: bepanhtai@gmail.com</p>
            <p>Fanpage: Bếp Anh Tài</p>
            <p>Zalo OA: Bếp Anh Tài Dạy Nấu Ăn</p>
        </div>

        <div class="footer-section">
            <h4>Liên kết</h4>
            <a href="ve-chung-toi.php">Về Chúng Tôi</a>
            <div class="social-icons">
                <a href="https://www.facebook.com/na.ny.le.2024" target="_blank" aria-label="Facebook">
                    <img src="images/logoface.png" alt="Facebook">
                </a>
                <a href="https://www.tiktok.com/@nynaxinchaomn?lang=vi-VN" target="_blank" aria-label="TikTok">
                    <img src="images/logotiktok.png" alt="TikTok">
                </a>
            </div>
        </div>
    </footer>
    
    <?php if (isset($extraJs)) : ?>
        <script src="<?php echo $extraJs; ?>"></script>
    <?php endif; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const params = new URLSearchParams(window.location.search);
            if (params.has("page")) {
                setTimeout(() => {
                    const target = document.querySelector(".dishes-grid");
                    if (target) {
                        const yOffset = -80; 
                        const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({ top: y, behavior: 'smooth' });
                    }
                }, 300); 
            }
        });
    </script>

    <script>
        function scrollToDishes() {
            const params = new URLSearchParams(window.location.search);
            if (params.has("page")) {
                const target = document.querySelector(".dishes-grid");
                if (target) {
                    const yOffset = -80;
                    const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(scrollToDishes, 300);
        });

        window.addEventListener("load", () => {
            setTimeout(scrollToDishes, 300);
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.querySelector('.user-toggle');
        const menu = document.querySelector('.user-menu');
        if (!toggle || !menu) return;
        toggle.addEventListener('click', e => {
            e.stopPropagation();
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });
        document.addEventListener('click', () => {
            menu.style.display = 'none';
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationWrapper = document.querySelector('.notification-wrapper');
        const notificationButton = document.querySelector('.notification-button');
        const notificationCount = document.getElementById('notificationCount');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');

        // Biến này được PHP set, nó phản ánh đúng trạng thái đăng nhập
        const userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        // Nếu người dùng chưa đăng nhập, thoát khỏi script này
        if (!userLoggedIn) {
            // Đảm bảo badge ẩn khi chưa đăng nhập
            if(notificationCount) { // Kiểm tra để tránh lỗi nếu phần tử không tồn tại
                notificationCount.style.display = 'none';
            }
            return; 
        }

        // Hàm để cập nhật số thông báo chưa đọc
        function updateUnreadCount() {
            // Sử dụng fetch API để gọi đến chính layout.php với action getUnreadCount
            fetch('<?php echo BASE_PATH; ?>/layout.php?action=getUnreadCount')
                .then(response => {
                    // Log the raw response for debugging
                    // console.log('Raw getUnreadCount response:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // console.log('getUnreadCount data:', data); // Debug: xem dữ liệu trả về
                    if (data.success && typeof data.count !== 'undefined') {
                        if (data.count > 0) {
                            notificationCount.textContent = data.count;
                            notificationCount.style.display = 'flex'; // Hiển thị số nếu có thông báo
                        } else {
                            notificationCount.textContent = ''; // Xóa số đếm
                            notificationCount.style.display = 'none'; // Ẩn số nếu không có thông báo
                        }
                    } else {
                        console.error('Lỗi khi lấy số thông báo chưa đọc:', data.message || 'Không rõ lỗi.');
                        notificationCount.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Lỗi mạng hoặc phân tích JSON khi lấy số thông báo chưa đọc:', error);
                    notificationCount.style.display = 'none';
                });
        }

        // Hàm để tải và hiển thị danh sách thông báo
        function loadNotifications() {
            notificationList.innerHTML = '<li class="loading-notifications">Đang tải thông báo...</li>'; // Hiển thị trạng thái tải
            fetch('<?php echo BASE_PATH; ?>/layout.php?action=getNotifications')
                .then(response => {
                    // console.log('Raw getNotifications response:', response); // Debug: xem phản hồi thô
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // console.log('getNotifications data:', data); // Debug: xem dữ liệu trả về
                    notificationList.innerHTML = ''; // Xóa trạng thái tải
                    if (data.success && data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notif => {
                            const li = document.createElement('li');
                            li.classList.add('notification-item');
                            if (!notif.is_read) {
                                li.classList.add('unread');
                            }
                            li.setAttribute('data-notification-id', notif.id); // Lưu ID để đánh dấu đã đọc
                            
                            li.innerHTML = `
                                <a href="${notif.link}" class="notification-link">
                                    <div class="notification-message">${notif.message}</div>
                                    <div class="notification-date">${notif.created_at}</div>
                                    ${!notif.is_read ? '<span class="read-dot"></span>' : ''}
                                </a>
                            `;
                            notificationList.appendChild(li);
                        });

                        // Thêm sự kiện click để đánh dấu đã đọc cho từng mục thông báo
                        notificationList.querySelectorAll('.notification-item').forEach(item => {
                            item.addEventListener('click', function(event) {
                                const notifId = this.dataset.notificationId;
                                // Đánh dấu đã đọc bất kể click vào link hay không
                                markNotificationAsRead(notifId);
                                // Không ngăn chặn hành vi mặc định của thẻ <a> (nếu click vào link)
                                // Nếu bạn muốn ngăn chặn hành vi mặc định của link và xử lý chuyển hướng bằng JS, 
                                // hãy thêm event.preventDefault(); vào đây và dùng window.location.href = notif.link; sau khi đánh dấu đọc.
                            });
                        });

                    } else {
                        notificationList.innerHTML = '<li class="no-notifications">Không có thông báo mới.</li>';
                    }
                    // Sau khi tải thông báo, cập nhật lại số đếm (để đảm bảo đồng bộ)
                    updateUnreadCount();
                })
                .catch(error => {
                    console.error('Lỗi mạng hoặc phân tích JSON khi tải thông báo:', error);
                    notificationList.innerHTML = '<li class="error-notifications">Không thể tải thông báo. Vui lòng thử lại.</li>';
                });
        }

        // Hàm để đánh dấu thông báo đã đọc
        function markNotificationAsRead(notificationId) {
            const formData = new FormData();
            formData.append('notification_id', notificationId);

            fetch('<?php echo BASE_PATH; ?>/layout.php?action=markAsRead', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // console.log('Raw markAsRead response:', response); // Debug: xem phản hồi thô
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // console.log('markAsRead data:', data); // Debug: xem dữ liệu trả về
                if (data.success) {
                    // Cập nhật giao diện: xóa class 'unread' và chấm đỏ
                    const item = notificationList.querySelector(`li[data-notification-id="${notificationId}"]`);
                    if (item) {
                        item.classList.remove('unread');
                        const readDot = item.querySelector('.read-dot');
                        if (readDot) {
                            readDot.remove();
                        }
                    }
                    updateUnreadCount(); // Cập nhật lại số đếm sau khi đánh dấu đọc
                } else {
                    console.error('Lỗi khi đánh dấu đã đọc:', data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi mạng hoặc phân tích JSON khi đánh dấu đã đọc:', error);
            });
        }

        // Xử lý sự kiện click vào nút thông báo
        if (notificationButton) {
            notificationButton.addEventListener('click', function(event) {
                event.preventDefault(); // Ngăn hành vi mặc định của thẻ <a>
                notificationDropdown.classList.toggle('active'); // Chuyển đổi class 'active'

                if (notificationDropdown.classList.contains('active')) {
                    loadNotifications(); // Tải thông báo khi mở dropdown
                }
            });
        }

        // Đóng cửa sổ thông báo khi click ra ngoài
        document.addEventListener('click', function(event) {
            if (notificationWrapper && !notificationWrapper.contains(event.target) && notificationDropdown.classList.contains('active')) {
                notificationDropdown.classList.remove('active');
            }
        });
        
        // Tải số thông báo chưa đọc khi trang vừa tải xong
        // Chỉ gọi khi userLoggedIn là true, đã được xử lý ở đầu script
        updateUnreadCount();

        // Tải lại số thông báo mỗi 60 giây (tùy chọn, có thể gây tải server nếu lượng truy cập lớn)
        // setInterval(updateUnreadCount, 60000); 
    });
    </script>

            <script src="assets/js/favorite.js"></script>

        <?php if (basename($_SERVER['SCRIPT_NAME']) === 'yeuthich.php'): ?>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".favorite-icon").forEach(icon => {
                icon.addEventListener("click", () => {
                    const card = icon.closest(".dish-card");
                    const postId = card.dataset.postId;

                    fetch("ajax/remove_favorite.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "post_id=" + encodeURIComponent(postId)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            card.remove(); // Xoá bài viết khỏi DOM
                        } else {
                            alert("Xoá thất bại!");
                        }
                    });
                });
            });
        });
        </script>
        <?php endif; ?>

        <script src="assets/js/notification.js"></script>

    </body>
</html>