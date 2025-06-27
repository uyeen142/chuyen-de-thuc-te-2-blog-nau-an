document.addEventListener("DOMContentLoaded", () => {
    const notifIcon = document.querySelector(".notification-icon");
    if (!notifIcon) return;

    notifIcon.addEventListener("click", (e) => {
        if (notifIcon.classList.contains("disabled")) {
            e.preventDefault();
            showPopup("Bạn cần đăng nhập để nhận thông báo mới nhất về bài viết");
        }
    });
});

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
