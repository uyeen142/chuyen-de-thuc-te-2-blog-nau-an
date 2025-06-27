document.addEventListener("DOMContentLoaded", () => {
    const isLoggedIn = document.querySelector(".header-favorite:not(.disabled)") !== null;

    if (isLoggedIn) {
        updateFavoriteCount(); // ✅ Chỉ cập nhật số nếu đã đăng nhập
    }

    // Gắn sự kiện click cho các icon yêu thích dưới mỗi bài viết
    document.querySelectorAll(".favorite-icon").forEach(icon => {
        icon.addEventListener("click", () => {
            if (icon.dataset.loading === "true") return;

            if (icon.classList.contains("disabled")) {
                showPopup("Bạn cần đăng nhập để sử dụng tính năng yêu thích");
                return;
            }

            const card = icon.closest(".dish-card");
            const postId = card.dataset.postId;
            const heartIcon = icon.querySelector("i");
            const isFavorited = heartIcon.classList.contains("fas");
            const url = isFavorited ? "ajax/remove_favorite.php" : "ajax/add_to_favorite.php";

            icon.dataset.loading = "true";

            fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `post_id=${encodeURIComponent(postId)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (isFavorited) {
                        heartIcon.classList.replace("fas", "far");

                        // Nếu đang ở trang yeuthich.php => xoá bài luôn
                        if (window.location.href.includes("yeuthich.php")) {
                            card.remove();

                            requestAnimationFrame(() => {
                                const remaining = document.querySelectorAll(".dish-card");
                                if (remaining.length === 0) {
                                    document.querySelector(".no-favorites-message")?.remove();

                                    const message = document.createElement("p");
                                    message.className = "no-favorites-message";
                                    message.textContent = "Bạn chưa thêm bài viết nào vào danh sách yêu thích.";
                                    document.querySelector(".dishes-section").appendChild(message);
                                }
                            });
                        }
                    } else {
                        heartIcon.classList.replace("far", "fas");
                    }

                    updateFavoriteCount(); // ✅ Cập nhật lại số
                } else {
                    showPopup(data.message || "Có lỗi xảy ra.");
                }
            })
            .catch(() => {
                showPopup("Không thể gửi yêu cầu. Kiểm tra mạng?");
            })
            .finally(() => {
                icon.dataset.loading = "false";
            });

            // ✅ Gửi thêm bằng sendBeacon (dự phòng)
            const beaconData = new URLSearchParams();
            beaconData.append("post_id", postId);
            navigator.sendBeacon(url, beaconData);
        });
    });

    // ✅ Click icon yêu thích trên HEADER khi chưa đăng nhập
const headerFavLink = document.querySelector("a.header-favorite.disabled");

if (headerFavLink) {
    headerFavLink.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        showPopup("Bạn cần đăng nhập để sử dụng tính năng yêu thích");
    });
}

// ✅ Cập nhật số lượng yêu thích
function updateFavoriteCount() {
    fetch('ajax/get_favorite_count.php')
        .then(res => res.json())
        .then(data => {
            const favCount = document.getElementById("favoriteCount");
            if (!favCount) return;

            if (data.success && data.count > 0) {
                favCount.textContent = data.count;
                favCount.style.display = 'flex';
            } else {
                favCount.style.display = 'none';
            }
        })
        .catch(err => {
            console.error("Lỗi khi lấy số yêu thích:", err);
        });
}

// ✅ Hiển thị popup ở giữa màn hình
function showPopup(message) {
    const popup = document.createElement("div");
    popup.innerText = message;
    popup.style.position = "fixed";
    popup.style.top = "50%";
    popup.style.left = "50%";
    popup.style.transform = "translate(-50%, -50%)";
    popup.style.background = "#fff";
    popup.style.padding = "20px 30px";
    popup.style.border = "1px solid #ccc";
    popup.style.borderRadius = "8px";
    popup.style.boxShadow = "0 5px 15px rgba(0,0,0,0.2)";
    popup.style.zIndex = 9999;
    popup.style.fontSize = "16px";

    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 2500);
}
});
