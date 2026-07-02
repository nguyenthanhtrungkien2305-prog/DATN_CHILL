@extends('admin.layouts.app')

@section('title', 'Quản lý Topping - Chill Chill Admin')
@section('page_title', 'Danh sách Topping')

@section('content')
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500">Quản lý các loại topping bán kèm món nước.</p>
    <a href="{{ route('toppings.create') }}" class="bg-[#e8634a] text-white px-6 py-2 rounded-lg hover:bg-[#d5523b] transition font-medium">
        + Thêm Topping mới
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                <th class="p-4 font-medium w-20">ID</th>
                <th class="p-4 font-medium w-32">Hình ảnh</th>
                <th class="p-4 font-medium">Tên Topping</th>
                <th class="p-4 font-medium">Giá tiền</th>
                <th class="p-4 font-medium text-center w-40">Hành động</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 text-sm">
            @foreach($toppings as $top)
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="p-4">{{ $top->topping_id }}</td>
                <td class="p-4">
                    <img src="{{ $top->image ?? 'https://via.placeholder.com/150' }}" alt="{{ $top->name }}" class="w-16 h-12 rounded object-cover border">
                </td>
                <td class="p-4 font-medium text-gray-900 text-base">{{ $top->name }}</td>
                <td class="p-4 font-bold text-[#e8634a]">{{ number_format($top->price, 0, ',', '.') }} đ</td>
                <td class="p-4 flex justify-center gap-4 mt-2">
                    <a href="{{ route('toppings.edit', $top->topping_id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Sửa</a>
                    
                    <form action="{{ route('toppings.destroy', $top->topping_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa topping này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $toppings->links() }}</div>
</div>
@endsection