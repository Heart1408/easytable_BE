<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use TheSeer\Tokenizer\Exception;
use App\Models\Staff;
use Validator;

class StaffController extends Controller
{
    public function get_list(Request $request)
    {
        try {
            $data = $request->all();
            $chainstore_id = $request->user()->chain_store_id;
            $staffs = Staff::where('chain_store_id', $chainstore_id);

            return response()->json([
                'success' => true,
                'current_staff_id' => $request->user()->id,
                'total_records' => $staffs->count(),
                'data' => $staffs->search($data)->get(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function create(Request $request)
    {
        $data = $request->only('username', 'fullname', 'email', 'phone', 'password', 'password_confirmation', 'permission');
        $validator = Validator::make($data, [
            'username' => 'required|unique:staffs|max:30',
            'email' => 'nullable|email',
            'fullname' => 'nullable',
            'phone' => 'required|regex:/(03)[0-9]{8}/|unique:staffs',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'permission' => 'required',
        ], [
                'username.required' => 'Tên đăng nhập không được bỏ trống!',
                'username.unique' => 'Tên đăng nhập đã tồn tại!',
                'username.max' => 'Tên đăng không được quá 30 ký tự!',
                'email.email' => 'Email không hợp lệ!',
                'phone.required' => 'Số điện thoại không được bỏ trống!',
                'phone.regex' => 'Số điện thoại không hợp lệ!',
                'phone.unique' => 'Số điện thoại đã tồn tại!',
                'password.required' => 'Mật khẩu không được bỏ trống!',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự!',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp!',
                'password_confirmation.required' => 'Xác nhận lại mật khẩu!',
                'permission.required' => 'Hãy chọn vai trò!',
            ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $chainstore_id = $request->user()->chain_store_id;

        try {
            Staff::create([
                'username' => $data['username'],
                'fullname' => $data['fullname'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'permission' => $data['permission'],
                'chain_store_id' => $chainstore_id,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => "Thêm nhân viên thành công!",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request)
    {
        $id = $request->input('staff_id');
        $data = $request->only('username', 'fullname', 'email', 'phone', 'password', 'permission');
        $validator = Validator::make($data, [
            'username' => 'required|unique:staffs,username,' . $id . ',id|max:30',
            'email' => 'nullable|email',
            'fullname' => 'nullable',
            'phone' => 'required|regex:/(03)[0-9]{8}/|unique:staffs,phone,' . $id . ',id',
            'password' => 'nullable|min:6',
            'permission' => 'required',
        ], [
                'username.required' => 'Tên đăng nhập không được bỏ trống!',
                'username.unique' => 'Tên đăng nhập đã tồn tại!',
                'username.max' => 'Tên đăng không được quá 30 ký tự!',
                'email.email' => 'Email không hợp lệ!',
                'phone.required' => 'Số điện thoại không được bỏ trống!',
                'phone.regex' => 'Số điện thoại không hợp lệ!',
                'phone.unique' => 'Số điện thoại đã tồn tại!',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự!',
                'permission.required' => 'Hãy chọn vai trò!',
            ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $chainstore_id = $request->user()->chain_store_id;

        try {
            Staff::find($id)->update([
                'username' => $data['username'],
                'fullname' => $data['fullname'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'permission' => $data['permission'],
                'chain_store_id' => $chainstore_id,
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => "Cập nhật thành công!",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {
            Staff::findOrFail($id)->delete();
            $result = [
                'status' => 200,
                'success' => true,
                'message' => "Xóa thành công!",
            ];
        } catch (ModelNotFoundException $e) {
            $result = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }
}