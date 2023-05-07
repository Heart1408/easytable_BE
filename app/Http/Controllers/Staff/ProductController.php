<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TheSeer\Tokenizer\Exception;
use App\Models\Dish;
use Validator;

class ProductController extends Controller
{
  public function get_list(Request $request)
  {
    try {
      $data = $request->all();
      $dish = Dish::search($data)->get();

      return response()->json([
        'success' => true,
        'data' => $dish,
      ]);
    } catch (Exception $e) {
      return response()->json([
        'status' => 500,
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
  }
  public function add(Request $request)
  {
    try {
      $validator = Validator::make(
        $request->all(),
        [
          'name' => 'required|max:150',
          'dish_type' => 'required',
          'price' => 'required|numeric|min:10000',
          'image' => 'required|image|mimes:jpeg,png,svg|max:5120',
        ],
        [
          'name.required' => 'Vui lòng nhập tên sản phẩm!',
          'dish_type.required' => 'Vui lòng chọn danh mục sản phẩm!',
          'price.required' => 'Vui lòng nhập giá sản phẩm!',
          'price.min' => 'Giá phải lớn hơn 10.000 đồng!',
          'image.required' => 'Vui lòng chọn hình ảnh sản phẩm!',
          'image.image' => 'Định dạng ảnh không hợp lệ!',
          'image.mimes' => 'Định dạng ảnh không được hỗ trợ!',
          'image.max' => 'Kích thước ảnh không được vượt quá 5MB!',
        ]
      );

      if ($validator->fails()) {
        $errors = $validator->messages()->all();

        return response()->json([
          'success' => false,
          'message' => $errors,
        ], 400);
      }

      $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
      $imagePath = $request->file('image')->storeAs('public', $imageName);
      $imageUrl = Storage::url($imagePath);

      $dish = Dish::create([
        'name' => $request->input('name'),
        'dish_type_id' => $request->input('dish_type'),
        'price' => $request->input('price'),
        'image' => $imageUrl,
      ]);

      if ($request->input('status')) {
        $chainstore_id = $request->user()->chain_store_id;
        $dish->chain_stores()->attach($chainstore_id, ['status' => $request->input('status')]);
      }

      return response()->json([
        'success' => true,
        'token' => 'Thêm sản phẩm thành công!',
      ], 200);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}