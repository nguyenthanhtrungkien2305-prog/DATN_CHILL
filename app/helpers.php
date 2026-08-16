<?php

use Illuminate\Support\Str;

if (!function_exists('format_image_url')) {
    function format_image_url(?string $url, string $default = '/images/logo1.jpg', ?string $name = null): string {
        // 1. Kiểm tra nếu có tên sản phẩm -> Tự động ánh xạ ảnh thực tế trong public/images
        if (!empty($name)) {
            $slug = Str::slug($name, '');
            $nameMap = [
                'tradualuoi' => 'images/tradualuoi.jpg',
                'chocolatedaxay' => 'images/chocolatedaxay.jpg',
                'carameldaxay' => 'images/carameldaxay.jpg',
                'bacsiu' => 'images/bacsiu.jpg',
                'sodachanh' => 'images/sodachanh.jpg',
                'sodadau' => 'images/sodadau.jpg',
                'sodavietquat' => 'images/sodavietquac.jpg',
                'sodavietquac' => 'images/sodavietquac.jpg',
                'camvat' => 'images/camep.jpg',
                'hongtrasua' => 'images/hongtrasua.png',
                'travai' => 'images/travai.jpg',
                'sinhtodau' => 'images/dautaydaxay.jpg',
                'tradaocamsa' => 'images/tradaocamxa.jpg',
                'nuocepcarot' => 'images/epcarot.jpg',
                'nuocepthom' => 'images/epthom.jpg',
                'nuocep' => 'images/epthom.jpg',
                'capheden' => 'images/capheden.jpg',
                'matchadaxay' => 'images/matchadaxay.jpg',
                'trachanhvang' => 'images/trachanhvang.jpg',
                'trasoai' => 'images/trasoai.jpg',
                'tradau' => 'images/tradau.jpg',
                'pannacotta' => 'images/pannacotta.jpg',
                'tiramisu' => 'images/tiramisu.jpg',
                'cheesecakechanhday' => 'images/Cheesecakechanhday.jpg',
                'cheesecakevietquoc' => 'images/Cheesecakevietquoc.jpg',
                'croissantbo' => 'images/croissantbo.jpg',
                'croissantchocolate' => 'images/Croissantchocolate.jpg',
                'banhmiphomai' => 'images/banhmiphomai.jpg',
                'banhbonglantrungmuoi' => 'images/banhbonglantrungmuoi.jpg',
                'banhsukem' => 'images/banhsukem.jpg',
                'puddingtrung' => 'images/puddingtrung.jpg',
                'tranchauden' => 'images/chanchauden.jpg',
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

        if (empty($url)) {
            return asset(ltrim($default, '/'));
        }

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

        // Kiểm tra ảnh default truyền vào
        $defClean = ltrim($default, '/');
        if (file_exists(public_path($defClean))) {
            return asset($defClean);
        }

        return asset('images/logo1.jpg');
    }
}
