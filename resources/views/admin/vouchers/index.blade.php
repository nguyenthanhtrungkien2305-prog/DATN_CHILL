<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Mã Giảm Giá - Chill Chill Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')

    {{-- NỘI DUNG CHÍNH --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8">
            <h2 class="text-xl font-semibold text-gray-800">Danh sách Mã Giảm Giá</h2>
        </header>

        <div class="p-8">
            {{-- Thông báo --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-500">Quản lý các chương trình ưu đãi, mã giảm giá áp dụng cho khách mua hàng.</p>
                <a href="{{ route('vouchers.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
                    + Thêm mã giảm giá mới
                </a>
            </div>

            {{-- Thanh tìm kiếm --}}
            <div class="bg-white p-4 rounded-t-2xl border-t border-x border-gray-100 flex items-center justify-between">
                <form action="{{ route('vouchers.index') }}" method="GET" class="w-full max-w-md flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã giảm giá..." class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-[#e8634a] text-sm">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">Tìm kiếm</button>
                    @if(request('search'))
                        <a href="{{ route('vouchers.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm flex items-center">Reset</a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                            <th class="p-4 font-medium w-16">ID</th>
                            <th class="p-4 font-medium">Mã ưu đãi</th>
                            <th class="p-4 font-medium">Loại giảm</th>
                            <th class="p-4 font-medium">Mức giảm</th>
                            <th class="p-4 font-medium">Đơn tối thiểu</th>
                            <th class="p-4 font-medium">Giới hạn sử dụng</th>
                            <th class="p-4 font-medium">Đã dùng</th>
                            <th class="p-4 font-medium">Thời gian hiệu lực</th>
                            <th class="p-4 font-medium text-center w-36">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($vouchers as $v)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-500">{{ $v->voucher_id }}</td>
                            <td class="p-4 font-bold text-espresso text-base"><span class="bg-orange-50 border border-orange-200 text-[#e8634a] px-2.5 py-1 rounded-md">{{ $v->code }}</span></td>
                            <td class="p-4">
                                @if($v->discount_type === 'percent')
                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-600 font-bold">Phần trăm (%)</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-600 font-bold">Số tiền cố định</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-gray-900">
                                @if($v->discount_type === 'percent')
                                    {{ number_format($v->discount_value, 0) }}%
                                @else
                                    {{ number_format($v->discount_value, 0, ',', '.') }}đ
                                @endif
                            </td>
                            <td class="p-4 font-medium">{{ number_format($v->min_order, 0, ',', '.') }}đ</td>
                            <td class="p-4 text-gray-600">
                                {{ $v->usage_limit ? $v->usage_limit . ' lượt' : 'Không giới hạn' }}
                            </td>
                            <td class="p-4 font-medium text-[#e8634a]">{{ $v->used_count }}</td>
                            <td class="p-4 text-xs text-gray-500">
                                <div>Bắt đầu: {{ $v->start_date ? \Carbon\Carbon::parse($v->start_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                                <div class="mt-1">Kết thúc: {{ $v->end_date ? \Carbon\Carbon::parse($v->end_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                            </td>
                            <td class="p-4 flex justify-center gap-4 mt-2">
                                <a href="{{ route('vouchers.edit', $v->voucher_id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Sửa</a>
                                
                                <form action="{{ route('vouchers.destroy', $v->voucher_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-400 italic">Chưa có mã giảm giá nào được tạo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{-- Phân trang --}}
                <div class="p-4 border-t">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </main>

</body>
</html>
