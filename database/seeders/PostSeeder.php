<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('posts')->truncate();

        $posts = [
            [
                'post_id' => 1,
                'title' => 'BÍ QUYẾT PHƯƠNG PHÁP RANG XAY CÀ PHÊ NGUYÊN BẢN TẠI CHILL CHILL',
                'slug' => 'bi-quyet-phuong-phap-rang-xay-ca-phe-nguyen-ban-tai-chill-chill',
                'content' => "Cà phê không chỉ là một thức uống quen thuộc mỗi buổi sáng, mà đối với Chill Chill, đó là cả một hành trình nghệ thuật gói trọn tâm huyết từ những đồi cà phê bạt ngàn vùng đất Tây Nguyên.\n\n1. Nguồn nguyên liệu được tuyển chọn khắt khe\nĐể tạo nên hương vị mộc mạc nhưng đậm đà khó quên, Chill Chill tuyển chọn từng hạt cà phê Robusta và Arabica đạt độ chín hoàn hảo. Những hạt cà phê chín mọng được hái thủ công, trải qua quá trình sơ chế nghiêm ngặt nhằm giữ nguyên hàm lượng hương vị tự nhiên nhất.\n\n2. Nghệ thuật rang xay chuẩn gu Việt\nBí quyết tạo nên điểm đặc trưng của cà phê Chill Chill nằm ở công nghệ rang xay hiện đại kết hợp với công thức gia truyền. Hạt cà phê được rang ở nhiệt độ thích hợp để dậy lên mùi thơm nồng nàn, chút đắng thanh hòa quyện cùng vị hậu ngọt sâu lắng. Dù thưởng thức Phin truyền thống hay Espresso hiện đại, bạn đều sẽ cảm nhận được sự tỉ mỉ trong từng ngụm cà phê.\n\n3. Đánh thức năng lượng cho ngày mới\nMột tách cà phê đậm đà tại Chill Chill không chỉ giúp bạn tỉnh táo làm việc mà còn là nguồn cảm hứng sáng tạo tuyệt vời. Hãy ghé thăm chi nhánh gần nhất hoặc đặt hàng ngay để trải nghiệm hương vị cà phê nguyên bản nhé!",
                'thumbnail' => '/images/caphe.png',
                'status' => 1,
                'categories_post_id' => 1,
                'auth_id' => 1,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'post_id' => 2,
                'title' => 'BẠC XỈU SÀI GÒN - HƯƠNG VỊ KÝ ỨC ĐỌC LẠI TRONG TỪNG NGỤM CHILL',
                'slug' => 'bac-xiu-sai-gon-huong-vi-ky-uc-doc-lai-trong-tung-ngum-chill',
                'content' => "Nếu cà phê đen đá mang vị đắng nguyên bản cá tính, thì Bạc Xỉu lại là sự vỗ về dịu ngọt cho những ai yêu thích sự nhẹ nhàng nhưng vẫn muốn vương vấn chút hương vị cà phê đậm đà.\n\n1. Nguồn gốc của ly Bạc Xỉu thân thuộc\nBạc Xỉu (viết tắt từ 'Bạc tẩy xỉu phạ') vốn bắt nguồn từ nét văn hóa ẩm thực giao thoa độc đáo của Sài Gòn xưa. Đối với nhiều người, Bạc Xỉu là món uống nhập môn đưa họ bước vào thế giới cà phê đầy say mê.\n\n2. Điểm đặc trưng tại Chill Chill Coffee & Tea\nTại Chill Chill, ly Bạc Xỉu được chăm chút với tỉ lệ vàng giữa lớp sữa đặc ngọt béo, sữa tươi thơm ngậy và cốt cà phê Robusta sánh mịn. Khi khuấy đều, màu nâu sóng sánh hiện lên đẹp mắt, vị ngọt béo ngậy hòa cùng hương thơm đắng dịu lan tỏa ngay từ ngụm đầu tiên.\n\n3. Thưởng thức trọn vẹn từng khoảnh khắc\nMột ly Bạc Xỉu mát lạnh cùng chiếc bánh ngọt vừa nướng chín tới sẽ là combo hoàn hảo cho buổi trò chuyện cùng bạn bè hay những giờ làm việc thăng hoa.",
                'thumbnail' => '/images/bacsiu.jpg',
                'status' => 1,
                'categories_post_id' => 1,
                'auth_id' => 1,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'post_id' => 3,
                'title' => 'HÀNH TRÌNH TỪ ĐỒI TRÀ XANH MƯỚT ĐẾN LY TRÀ TRÁI CÂY THANH MÁT',
                'slug' => 'hanh-trinh-tu-doi-tra-xanh-muot-den-ly-tra-trai-cay-thanh-mat',
                'content' => "Bên cạnh cà phê nguyên chất, dòng Trà Trái Cây tại Chill Chill luôn là sự lựa chọn hàng đầu của giới trẻ nhờ hương vị thanh mát, tự nhiên và vô cùng giải nhiệt.\n\n1. Búp trà tươi ủ lạnh chuẩn vị\nChill Chill sử dụng những búp trà Oolong và Trà Xanh tươi nguyên bản được trồng trên các vùng đồi trà sương mù. Trà được ủ ở nhiệt độ tiêu chuẩn để chiết xuất trọn vẹn hương thơm thanh khiết, chát nhẹ hậu ngọt mà không hề bị đắng chát.\n\n2. Sự kết hợp bùng nổ cùng trái cây tươi\nMỗi ly trà là sự kết hợp ăn ý giữa nền trà đậm đà và các loại trái cây tươi ngon như Đào, Dâu tây, Cam sả, Vải hay Mãng cầu. Trái cây được chế biến tươi mỗi ngày, giữ trọn vị giòn ngọt và vitamin thiên nhiên.\n\n3. Thức uống tươi mát bảo vệ sức khỏe\nKhông chỉ mang lại cảm giác sảng khoái tức thì, trà trái cây Chill Chill còn chứa nhiều chất chống oxy hóa, hỗ trợ thanh lọc cơ thể và nạp lại nguồn năng lượng tươi trẻ cho bạn.",
                'thumbnail' => '/images/tra.png',
                'status' => 1,
                'categories_post_id' => 2,
                'auth_id' => 1,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'post_id' => 4,
                'title' => 'THƯỞNG THỨC HỒNG TRÀ SỮA & TRÀ TRÁI CÂY NHIỆT ĐỚI ĐẶC BIỆT',
                'slug' => 'thuong-thuc-hong-tra-sua-tra-trai-cay-nhiet-doi-dac-biet',
                'content' => "Trà sữa và trà nhiệt đới luôn mang một sức hút đặc biệt đối với các tín đồ mê ẩm thực thức uống. Tại Chill Chill, chúng mình mang đến cho bạn danh mục trà đa dạng với hương vị cuốn hút độc đáo.\n\n1. Hồng Trà Sữa ngậy béo thơm lừng\nĐược pha chế từ nền Hồng Trà thượng hạng ủ đậm đà kết hợp cùng lớp kem sữa béo ngậy, ly Hồng Trà Sữa Chill Chill sở hữu vị ngọt thanh vừa phải, không gây ngấy. Thêm chút trân châu đen dẻo giòn hay trân châu hoàng kim là bạn đã có ngay ly trà sữa chuẩn gu.\n\n2. Trà Trái Cây Nhiệt Đới giải nhiệt cực đỉnh\nBản giao hưởng giữa vị chua ngọt hài hòa của Chanh vàng, Táo, Thơm (Dứa) và Dưa lưới đem lại cảm giác sảng khoái bất tận. Thức uống đầy màu sắc rực rỡ này chắc chắn sẽ làm bừng sáng góc chụp ảnh Check-in của bạn.\n\n3. Trải nghiệm dịch vụ giao hàng tận nơi\nDù ở văn phòng hay tại nhà, chỉ với vài thao tác đơn giản trên website, ly trà yêu thích của bạn sẽ được giao tới nhanh chóng, giữ trọn độ tươi ngon mát lạnh.",
                'thumbnail' => '/images/hongtrasua.png',
                'status' => 1,
                'categories_post_id' => 2,
                'auth_id' => 1,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'post_id' => 5,
                'title' => 'THƯỞNG THỨC BÁNH NGỌT TƯƠI MỖI NGÀY CÙNG THỨC UỐNG CHILL CHILL',
                'slug' => 'thuong-thuc-banh-ngot-tuoi-moi-ngay-cung-thuc-uong-chill-chill',
                'content' => "Một buổi trà chiều hoàn hảo không thể thiếu sự hiện diện của những chiếc bánh ngọt thơm phức, mềm mịn được nướng mới mỗi ngày tại Chill Chill.\n\n1. Thực đơn bánh ngọt phong phú chuẩn vị Tiệm Bánh\nTừ Croissant bơ tỏi giòn rụm thơm lừng, Tiramisu cacao đậm đà mềm tan, đến Cheesecake chanh dây chua ngọt thanh mát... tất cả đều được chế biến từ nguồn nguyên liệu nhập khẩu cao cấp.\n\n2. Sự bắt cặp ăn ý cùng cà phê & trà\n- Tiramisu kết hợp cùng Cà phê Espresso: Sự hòa quyện bùng nổ giữa vị đắng nhẹ và béo ngậy.\n- Croissant kết hợp cùng Trà Trái Cây: Giúp cân bằng vị giác, tạo cảm giác nhẹ nhàng sảng khoái.\n\n3. Điểm hẹn trà chiều lý tưởng\nHãy tạm gác lại những xô xà công việc để tự thưởng cho mình một góc nhỏ yên bình, nhâm nhi tách trà thơm và thưởng thức món bánh yêu thích cùng Chill Chill bạn nhé!",
                'thumbnail' => '/images/banhngot.png',
                'status' => 1,
                'categories_post_id' => 3,
                'auth_id' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'post_id' => 6,
                'title' => 'KHÁM PHÁ GÓI COMBO TIẾT KIỆM - UỐNG LÀ MÊ DÀNH CHO BẠN BÈ & CẶP ĐÔI',
                'slug' => 'kham-pha-goi-combo-tiet-kiem-uong-la-me-danh-cho-ban-be-cap-doi',
                'content' => "Thấu hiểu nhu cầu thưởng thức đa dạng cùng bạn bè và người thương, Chill Chill giới thiệu dòng Gói Combo Tiết Kiệm với mức giá ưu đãi cực hấp dẫn lên tới 30%.\n\n1. Đa dạng các gói Combo hấp dẫn\n- Combo Buổi Hẹn Ngọt Ngào: Trọn bộ 2 ly nước tùy chọn kèm 1 phần bánh Tiramisu mềm mịn.\n- Combo Bữa Sáng Năng Lượng: Tách Cà phê Phin đậm đà kết hợp cùng bánh Croissant giòn rụm.\n- Combo Giải Nhiệt Mùa Hè: Nhóm 3-4 ly Trà trái cây nhiệt đới với mức giá siêu ưu đãi.\n\n2. Đặt hàng dễ dàng - Ưu đãi trao tay\nMọi gói Combo đều được cập nhật thường xuyên trên trang chủ và chuyên mục Combo của website. Khách hàng có thể dễ dàng thêm vào giỏ hàng và đặt giao tận nơi nhanh chóng.\n\n3. Đồng hành cùng mọi cuộc vui\nKhông chỉ giúp tiết kiệm chi phí, các gói Combo Chill Chill còn mang tới sự tiện lợi và niềm vui trọn vẹn trong mọi dịp tụ họp, sinh nhật hay làm việc nhóm.",
                'thumbnail' => '/images/combobuoihen.png',
                'status' => 1,
                'categories_post_id' => 3,
                'auth_id' => 1,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insert($post);
        }
    }
}
