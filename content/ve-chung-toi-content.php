<section style="text-align: center; padding: 60px 20px; font-family: 'Roboto Serif', serif;">

    <h2 style="color: #c0392b; font-weight: bold;">
        Nấu ăn ngon hơn <em style="color: #6e0d0d;">cuộc sống hạnh phúc hơn!</em>
    </h2>

    <h3 style="font-size: 36px; font-weight: 400; margin-top: 20px; line-height: 1.6; margin-bottom: 40px;">
        <span style="color: #2c3e50;">Nấu ăn là niềm vui thay vì</span><br>
        <span style="color: #2c3e50;">thử thách</span>
    </h3>

    <img src="images/vechungtoi1.jpg" alt="Người đang nấu ăn" style="width: 95%; max-width: 900px; margin: 40px auto; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: block;">

    <!-- CHIA CỘT: BÊN TRÁI LÀ TEXT, BÊN PHẢI LÀ ẢNH -->
    <div style="display: flex; flex-wrap: wrap; max-width: 1200px; margin: 80px auto 60px auto; gap: 60px; justify-content: center; align-items: flex-start; text-align: left;">

        <!-- TEXT GIỚI THIỆU -->
        <div style="flex: 1 1 520px; min-width: 300px;">
            <h4 style="font-size: 32px; margin-bottom: 30px; font-weight: normal;">
                Về <span style="font-family: 'Great Vibes', cursive; color: #b72a2a; font-weight: 400;">Bếp Anh Tài</span>
            </h4>
            <p style="margin-bottom: 20px; text-align: justify;">
                Chào bạn, chúng mình là Bếp Anh Tài!<br>
                Chúng mình là một nhóm gồm 5 thành viên có chung niềm đam mê với nấu ăn. Từ tuổi sinh viên, cả nhóm đã mê mẩn với việc vào bếp,
                tìm tòi công thức, mày mò nấu nướng từ những món đơn giản đến cầu kỳ.
            </p>
            <p style="margin-bottom: 20px; text-align: justify;">
                Không học bài bản qua trường lớp, chúng mình học bằng trải nghiệm thực tế, bằng những lần thử sai và những bữa cơm đậm chất tình thân.
                Đó là lý do chúng mình cùng nhau tạo ra Bếp Anh Tài – nơi mỗi đứa trẻ đắm chìm, thể hiện sự quyết tâm và kinh nghiệm nấu ăn cho bất kỳ ai có cùng đam mê.
            </p>
            <p style="margin-bottom: 20px; text-align: justify;">
                Chúng mình tin rằng ai cũng có thể nấu ngon, chỉ cần bắt đầu từ những điều nhỏ nhất. Hy vọng website này sẽ là khoảng gian bếp thân thiện cho bạn tìm công thức, học hỏi, truyền cảm hứng, để cùng phát huy hương vị ẩm thực Việt Nam đậm đà hương vị.
            </p>
            <p style="text-align: justify;">
                Chào mừng bạn đến với Bếp Anh Tài – nơi đam mê nấu nướng được lan tỏa mỗi ngày!
            </p>
        </div>

        <!-- ẢNH NHÓM -->
        <div style="flex: 0 1 480px; margin-top: 40px;">
            <img src="images/vechungtoi2.jpg" alt="Ảnh nhóm" style="width: 100%; max-width: 100%; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        </div>
    </div>

 <!-- VÒNG CUNG ẢNH MÓN ĂN TO HƠN -->
<style>
.circle-arc-container {
    position: relative;
    width: 100%;
    margin: 80px auto 0 auto;
}

.circle-arc-wrapper {
    position: relative;
    width: 100%;
    height: 320px;
    margin: 160px auto;
}

.circle-arc-wrapper img {
    position: absolute;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    object-fit: cover;
    transform: translate(-50%, -50%);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: transform 0.3s;
}

.circle-arc-wrapper img:hover {
    transform: translate(-50%, -50%) scale(1.1);
}

.title-below {
    text-align: center;
    font-size: 22px;
    margin-top: 160px; /* Đẩy rõ xuống dưới hình tròn */
    letter-spacing: 1.2px; /* Giãn chữ */
    line-height: 1.8;      /* Giãn dòng */
    font-family: 'Roboto Serif', serif;
}

</style>

<!-- BỌC CHUNG PHẦN ẢNH VÀ DÒNG CHỮ -->
<div class="circle-arc-container">
    <div class="circle-arc-wrapper">
        <?php
        $images = ['circle1.png', 'circle2.png', 'circle3.png', 'circle4.png', 'circle5.png', 'circle6.png', 'circle7.png'];
        $count = count($images);

        for ($i = 0; $i < $count; $i++)  {
    $percent = $i / ($count - 1);
    $x = 5 + $percent * 90;  // 👈 Thay vì nhân 100, mình nhân 90 rồi cộng 5 để chừa lề hai bên
    $y = 60 * cos($percent * M_PI);
    echo '<img src="images/' . $images[$i] . '" style="left:' . $x . '%; top:' . (50 + $y) . '%;">';
}
        ?>
    </div>

    <p class="title-below">
        Bắt đầu học nấu ăn cùng 
        <span style="color: #b20000; font-family: 'Great Vibes', cursive;">
            Bếp Anh Tài
        </span> 
        nhé!
    </p>
</div>


