<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\DishType;

class CategoryController extends Controller
{
    public function get_list()
    {
        try {
            $data = DishType::all();
            $result = [
                'success' => true,
                'data' => $data,
            ];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }

    public function add(Request $request)
    {
        $type = $request->input('type');
        $validator = Validator::make($request->all(), [
            'type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        try {
            $check = DishType::where('type', $type)->first();
            if (is_null($check)) {
                $dishType = new DishType;
                $dishType->type = $type;
                $dishType->save();

                $result = [
                    'success' => true,
                    'message' => "Thêm danh mục sản phẩm thành công!",
                ];
            } else {
                $result = [
                    'success' => false,
                    'message' => "Danh mục sản phẩm đã tồn tại!",
                ];
            }
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }

    public function edit(Request $request)
    {
        $data = $request->only('id', 'type');
        $validator = Validator::make($data, [
            'type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        try {
            $dishType = DishType::find($data['id']);
            if (is_null($dishType)) {
                $result = [
                    'success' => false,
                    'message' => "Lỗi!",
                ];
            } else {
                $dishType->type = $data['type'];
                $dishType->save();

                $result = [
                    'success' => true,
                    'message' => "Cập nhật danh mục sản phẩm thành công!",
                ];
            }
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }

    public function delete(Request $request, $id)
    {
        try {
            DishType::findOrFail($id)->delete();

            $result = [
                'status' => 200,
                'success' => true,
                'message' => "Xóa danh mục sản phẩm thành công!",
            ];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }
}