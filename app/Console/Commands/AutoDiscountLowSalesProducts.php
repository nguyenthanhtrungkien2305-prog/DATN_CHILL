<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\ProductSalesController;

class AutoDiscountLowSalesProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:auto-discount-low-sales {--count=5 : Số lượng sản phẩm bán thấp cần giảm giá} {--min=2 : Mức giảm giá tối thiểu (%)} {--max=10 : Mức giảm giá tối đa (%)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động quét và áp dụng mức giảm giá từ 2% - 10% hàng tuần cho 5 sản phẩm có lượt mua ít nhất';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int)$this->option('count');
        $min = (int)$this->option('min');
        $max = (int)$this->option('max');

        if ($count < 1) $count = 5;
        if ($min < 1) $min = 2;
        if ($max > 90) $max = 10;
        if ($min > $max) $min = $max;

        $this->info("Đang quét các sản phẩm có lượt mua thấp nhất để áp dụng giảm giá ({$min}% - {$max}%)...");

        $results = ProductSalesController::autoApplyWeeklyDiscount($count, $min, $max, true);

        if (empty($results)) {
            $this->warn("Không tìm thấy sản phẩm nào phù hợp!");
            return 0;
        }

        $this->info("Đã áp dụng giảm giá tự động thành công cho " . count($results) . " sản phẩm:");
        
        $tableData = [];
        foreach ($results as $item) {
            $tableData[] = [
                'ID' => $item['product_id'],
                'Tên sản phẩm' => $item['name'],
                'Lượt đã bán' => $item['sold_count'],
                'Mức giảm giá (%)' => $item['discount_percent'] . '%'
            ];
        }

        $this->table(['ID', 'Tên sản phẩm', 'Lượt đã bán', 'Mức giảm giá (%)'], $tableData);

        return 0;
    }
}
