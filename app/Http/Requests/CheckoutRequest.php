<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:120',
            'customer_phone' => ['required', 'regex:/^(0|\+84)(\d){9,10}$/'],
            'customer_address' => 'required|string|max:255',
            'receive_at' => 'required|date|after_or_equal:' . now()->addHours(2)->format('Y-m-d H:i'),
            'note' => 'nullable|string|max:500',
            'payment_method' => 'required|in:cod,qr_code,bank_transfer',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ tên',
            'customer_name.max' => 'Họ tên không được quá 120 ký tự',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại',
            'customer_phone.regex' => 'Số điện thoại không đúng định dạng',
            'customer_address.required' => 'Vui lòng nhập địa chỉ',
            'customer_address.max' => 'Địa chỉ không được quá 255 ký tự',
            'receive_at.required' => 'Vui lòng chọn thời gian nhận hàng',
            'receive_at.date' => 'Thời gian nhận hàng không đúng định dạng',
            'receive_at.after_or_equal' => 'Thời gian nhận hàng phải sau ít nhất 2 giờ từ bây giờ',
            'note.max' => 'Ghi chú không được quá 500 ký tự',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
        ];
    }
}
