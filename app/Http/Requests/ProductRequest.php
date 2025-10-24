<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => 'required|string|max:180',
            'slug' => 'required|string|max:180|unique:products,slug,' . $productId,
            'sku' => 'required|string|max:50|unique:products,sku,' . $productId,
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'short_desc' => 'nullable|string|max:500',
            'long_desc' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'name.max' => 'Tên sản phẩm không được quá 180 ký tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'sku.required' => 'Vui lòng nhập mã SKU',
            'sku.unique' => 'Mã SKU đã tồn tại',
            'category_id.exists' => 'Danh mục không tồn tại',
            'price.required' => 'Vui lòng nhập giá sản phẩm',
            'price.integer' => 'Giá phải là số nguyên',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'sale_price.integer' => 'Giá khuyến mãi phải là số nguyên',
            'sale_price.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0',
            'short_desc.max' => 'Mô tả ngắn không được quá 500 ký tự',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg hoặc gif',
            'images.*.max' => 'Kích thước hình ảnh không được quá 2MB',
        ];
    }
}
