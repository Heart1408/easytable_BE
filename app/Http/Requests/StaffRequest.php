<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Validator;

class StaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|unique:staffs|max:30',
            'email' => 'nullable|email',
            'fullname' => 'nullable',
            'phone' => 'required|regex:/(03)[0-9]{8}/|unique:staffs',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'permission' => 'required',
        ];
    }

    public function messages()
    {
        return [
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
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }
}