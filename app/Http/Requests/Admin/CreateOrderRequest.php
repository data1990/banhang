<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,momo',
            'receive_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:999',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Tên khách hàng là bắt buộc',
            'customer_phone.required' => 'Số điện thoại là bắt buộc',
            'shipping_address.required' => 'Địa chỉ giao hàng là bắt buộc',
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'items.required' => 'Phải có ít nhất 1 sản phẩm',
            'items.*.product_id.required' => 'Sản phẩm là bắt buộc',
            'items.*.quantity.required' => 'Số lượng là bắt buộc',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            'items.*.price.required' => 'Giá sản phẩm là bắt buộc',
        ];
    }
}
