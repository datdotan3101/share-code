document.addEventListener("DOMContentLoaded", function () {
  console.log("Script.js đã tải!");

  document.addEventListener("click", function (event) {
    if (event.target.classList.contains("like-button")) {
      const postId = event.target.dataset.postId;
      let likeCountSpan = document.getElementById(`like-count-${postId}`);
      const buttonElement = event.target;

      console.log("Đang gửi yêu cầu like cho post ID:", postId);

      fetch("like.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `post_id=${postId}`,
        credentials: "include",
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Dữ liệu phản hồi từ server:", data);

          if (data.success) {
            // Nếu không tìm thấy <span>, tạo mới và thêm vào nút like
            if (!likeCountSpan) {
              likeCountSpan = document.createElement("span");
              likeCountSpan.id = `like-count-${postId}`;
              likeCountSpan.style.marginLeft = "5px"; // Thêm khoảng cách
              buttonElement.appendChild(likeCountSpan);
            }

            likeCountSpan.textContent = `(${data.like_count})`; // Cập nhật số like

            // Cập nhật nội dung nút like mà không mất <span>
            buttonElement.dataset.action = data.action;
            buttonElement.firstChild.textContent =
              data.action === "liked" ? "👎 Bỏ thích" : "👍 Thích";
          } else {
            alert("⚠ Lỗi: " + data.message);
          }
        })
        .catch((error) => console.error("Lỗi AJAX:", error));
    }
  });
});
