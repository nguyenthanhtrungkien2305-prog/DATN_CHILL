<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DistanceService
{
    // Tọa độ địa lý của QTSC 9 Building, Đường Tô Ký, Phường Trung Mỹ Tây, Quận 12, TP.HCM
    const STORE_LAT = 10.853744;
    const STORE_LNG = 106.628352;
    const STORE_ADDRESS = 'QTSC 9 Building, Tô Ký, Trung Mỹ Tây, Quận 12, Hồ Chí Minh';

    /**
     * Tính khoảng cách từ Quán QTSC 9 Tô Ký (Q.12) đến địa chỉ nhận hàng của khách
     * 
     * @param string $customerAddress
     * @return float Khoảng cách theo km
     */
    public static function calculateDistanceKm($customerAddress)
    {
        if (empty(trim($customerAddress))) {
            return 2.0; // Mặc định 2km nếu địa chỉ rỗng
        }

        $addrLower = mb_strtolower($customerAddress, 'UTF-8');

        // 1. KIỂM TRA QUẬN/HUYỆN TP.HCM VỚI REGEX BẢO VỆ RANH GIỚI TỪ (\b)
        
        // Gần quán: Trung Mỹ Tây, QTSC, Tân Chánh Hiệp
        if (str_contains($addrLower, 'trung mỹ tây') || str_contains($addrLower, 'qtsc') || str_contains($addrLower, 'tân chánh hiệp')) {
            return 1.2;
        }

        // Quận 12 / Hóc Môn
        if (preg_match('/quận\s*12\b|q\.?12\b|hóc môn\b/i', $customerAddress)) {
            return 3.8;
        }

        // Gò Vấp
        if (str_contains($addrLower, 'gò vấp')) {
            return 6.5;
        }

        // Tân Bình
        if (str_contains($addrLower, 'tân bình')) {
            return 7.5;
        }

        // Tân Phú
        if (str_contains($addrLower, 'tân phú')) {
            return 8.5;
        }

        // Phú Nhuận
        if (str_contains($addrLower, 'phú nhuận')) {
            return 9.5;
        }

        // Bình Thạnh
        if (str_contains($addrLower, 'bình thạnh')) {
            return 10.5;
        }

        // Quận 10
        if (preg_match('/quận\s*10\b|q\.?10\b/i', $customerAddress)) {
            return 10.8;
        }

        // Quận 11
        if (preg_match('/quận\s*11\b|q\.?11\b/i', $customerAddress)) {
            return 11.2;
        }

        // Quận 3
        if (preg_match('/quận\s*3\b|q\.?3\b/i', $customerAddress)) {
            return 11.5;
        }

        // Quận 1 (Tân Định, Bến Nghé, Bến Thành, Đa Kao, Phạm Ngũ Lão...)
        if (preg_match('/quận\s*1\b|q\.?1\b|tân định\b|bến thành\b|bến nghệ\b|bến nghé\b|đa kao\b/i', $customerAddress)) {
            return 12.5;
        }

        // Quận 5, Quận 6
        if (preg_match('/quận\s*5\b|q\.?5\b|quận\s*6\b|q\.?6\b/i', $customerAddress)) {
            return 13.5;
        }

        // Quận 4, Bình Tân
        if (preg_match('/quận\s*4\b|q\.?4\b|bình tân\b/i', $customerAddress)) {
            return 14.5;
        }

        // Thủ Đức / Quận 2 / Quận 9
        if (str_contains($addrLower, 'thủ đức') || preg_match('/quận\s*2\b|q\.?2\b|quận\s*9\b|q\.?9\b/i', $customerAddress)) {
            return 15.5;
        }

        // Quận 7, Quận 8
        if (preg_match('/quận\s*7\b|q\.?7\b|quận\s*8\b|q\.?8\b/i', $customerAddress)) {
            return 17.0;
        }

        // Củ Chi, Bình Chánh, Nhà Bè, Cần Giờ
        if (str_contains($addrLower, 'củ chi') || str_contains($addrLower, 'bình chánh') || str_contains($addrLower, 'nhà bè') || str_contains($addrLower, 'cần giờ')) {
            return 22.0;
        }

        // 2. Thử Geocoding API nếu không khớp danh mục trên
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'ChillChillCoffeeApp/1.0 (contact@chillchillcoffee.vn)'
            ])->timeout(3)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $customerAddress . ', Hồ Chí Minh, Việt Nam',
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    $customerLat = (float)$data[0]['lat'];
                    $customerLng = (float)$data[0]['lon'];

                    $straightKm = self::haversineGreatCircleDistance(
                        self::STORE_LAT, self::STORE_LNG,
                        $customerLat, $customerLng
                    );

                    $roadKm = round($straightKm * 1.30, 1);
                    return max(0.5, $roadKm);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Geocoding API Exception: " . $e->getMessage());
        }

        return 5.0; // Mặc định 5km
    }

    /**
     * Tính Phí Ship dựa trên số km
     * Bảng giá mới:
     * - Từ 5 km trở xuống: Miễn phí giao hàng (0đ)
     * - Từ trên 5 km đến 15 km (10km, 15km): 10.000đ
     * - Từ trên 15 km đến 25 km (20km, 25km): 30.000đ
     * - Trên 25 km: 30.000đ + 10.000đ cho mỗi 5 km thêm
     */
    public static function getShippingFee($distanceKm)
    {
        $dist = (float)$distanceKm;

        if ($dist <= 0) {
            return 0;
        }

        if ($dist <= 5.0) {
            return 0; // Free ship từ 5km trở xuống
        } elseif ($dist <= 15.0) {
            return 10000; // 10k cho khoảng từ trên 5km đến 15km (10km & 15km)
        } elseif ($dist <= 25.0) {
            return 30000; // 30k cho khoảng từ trên 15km đến 25km (20km & 25km)
        } else {
            // Trên 25km: 30.000đ + 10.000đ mỗi 5km vượt quá
            $extraBlocks = ceil(($dist - 25.0) / 5.0);
            return 30000 + ($extraBlocks * 10000);
        }
    }

    /**
     * Công thức Haversine tính khoảng cách theo đường chim bay
     */
    private static function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
