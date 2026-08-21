<?php

use Illuminate\Support\Str;

if (!function_exists('format_image_url')) {
    function format_image_url(?string $url, string $default = '/images/logo1.jpg', ?string $name = null): string {
        // 1. Kiểm tra nếu có tên sản phẩm / combo -> Tự động ánh xạ ảnh thực tế trong public/images
        if (!empty($name)) {
            $slug = Str::slug($name, '');
            $nameMap = [
                // Combos
                'combobuasangngotngao' => 'images/combobuasangngotngao.png',
                'combobuasang' => 'images/combobuasangmen.png',
                'combogiainhiet' => 'images/combogiainhiet.png',
                'combobuoihen' => 'images/combobuoihen.png',
                'combocapdoi' => 'images/combocapdoi.png',
                'combocuathang' => 'images/combocuathang.png',
                'combodentrang' => 'images/combodentrang.png',
                'combohacbach' => 'images/combodentrang.png',
                'combohatbach' => 'images/combodentrang.png',
                'combomuahe' => 'images/combomuahe.png',

                // Products
                'tradualuoi' => 'images/tradualuoi.jpg',
                'chocolatedaxay' => 'images/chocolatedaxay.jpg',
                'carameldaxay' => 'images/carameldaxay.jpg',
                'bacsiu' => 'images/bacsiu.jpg',
                'bacxiu' => 'images/bacsiu.jpg',
                'sodachanh' => 'images/sodachanh.jpg',
                'sodadau' => 'images/sodadau.jpg',
                'sodavietquat' => 'images/sodavietquac.jpg',
                'sodavietquoc' => 'images/sodavietquac.jpg',
                'sodavietquac' => 'images/sodavietquac.jpg',
                'camvat' => 'images/camep.jpg',
                'camep' => 'images/camep.jpg',
                'hongtrasua' => 'images/hongtrasua.png',
                'hongtrasua' => 'images/hongtrasua.png',
                'luctrasua' => 'images/luctrasua.jpg',
                'travai' => 'images/travai.jpg',
                'sinhtodau' => 'images/dautaydaxay.jpg',
                'dautaydaxay' => 'images/dautaydaxay.jpg',
                'tradaocamsa' => 'images/tradaocamxa.jpg',
                'tradaocamxa' => 'images/tradaocamxa.jpg',
                'nuocepcarot' => 'images/epcarot.jpg',
                'epcarot' => 'images/epcarot.jpg',
                'nuocepduahau' => 'images/epduahau.jpg',
                'epduahau' => 'images/epduahau.jpg',
                'nuoceptao' => 'images/eptao.jpg',
                'eptao' => 'images/eptao.jpg',
                'nuocepthom' => 'images/epthom.jpg',
                'epthom' => 'images/epthom.jpg',
                'nuocep' => 'images/epthom.jpg',
                'capheden' => 'images/capheden.jpg',
                'matchalatte' => 'images/matchadaxay.jpg',
                'matchadaxay' => 'images/matchadaxay.jpg',
                'trachanhvang' => 'images/trachanhvang.jpg',
                'trasoai' => 'images/trasoai.jpg',
                'tradau' => 'images/tradau.jpg',
                'trathanhlong' => 'images/trathanhlong.jpg',
                'tranhietdoi' => 'images/tranhietdoi.jpg',
                'pannacotta' => 'images/pannacotta.jpg',
                'tiramisu' => 'images/tiramisu.jpg',
                'cheesecakechanhday' => 'images/Cheesecakechanhday.jpg',
                'cheesecakevietquoc' => 'images/Cheesecakevietquoc.jpg',
                'cheesecakevietquat' => 'images/Cheesecakevietquoc.jpg',
                'croissantbo' => 'images/croissantbo.jpg',
                'croissantchocolate' => 'images/Croissantchocolate.jpg',
                'croissanchocolate' => 'images/Croissantchocolate.jpg',
                'banhmiphomai' => 'images/banhmiphomai.jpg',
                'banhbonglantrungmuoi' => 'images/banhbonglantrungmuoi.jpg',
                'banhsukem' => 'images/banhsukem.jpg',
                'puddingtrung' => 'images/puddingtrung.jpg',
                'tranchauden' => 'images/chanchauden.jpg',
                'chanchauden' => 'images/chanchauden.jpg',
                'tranchauhoangkim' => 'images/tranchauhoangkim.jpg',
                'tranchautrang' => 'images/tranchautrang.jpg',
                'thachcaphe' => 'images/thachcaphe.jpg',
                'thachdua' => 'images/thachdua.jpg',
            ];
            foreach ($nameMap as $key => $imgPath) {
                if (str_contains($slug, $key)) {
                    if (file_exists(public_path($imgPath))) {
                        return asset($imgPath);
                    }
                }
            }
        }

        // 2. Xử lý đường dẫn truyền vào trực tiếp
        if (!empty($url)) {
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return $url;
            }

            $clean = ltrim($url, '/');

            // Kiểm tra tệp thực tế tồn tại trong public
            if (file_exists(public_path($clean))) {
                return asset($clean);
            }

            // Tên tệp trực tiếp trong public/images
            $filename = basename($clean);
            if (file_exists(public_path('images/' . $filename))) {
                return asset('images/' . $filename);
            }
        }

        // 3. Kiểm tra ảnh default truyền vào
        $defClean = ltrim($default, '/');
        if (file_exists(public_path($defClean))) {
            return asset($defClean);
        }

        return asset('images/logo1.jpg');
    }
}
