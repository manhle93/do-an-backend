<?php

namespace App\Http\Controllers;

use App\SanPham;
use Illuminate\Http\Request;
use Validator;

class SanPhamController extends Controller
{
    public function __construct()
    {
        $this->middleware('JWT', ['except' => ['index']]);
    }

    public function addSanPham(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        $validator = Validator::make($data, [
            'ten_san_pham' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu thông tin'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            SanPham::create([
                'ten_san_pham' => $data['ten_san_pham'],
                'mo_ta' => $data['mo_ta'],
                'gia_ban' => $data['gia_ban'],
                'gia_nhap' => $data['gia_nhap'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'user_id' => $user->id,
            ]);
            return response(['message' => 'Thêm sản phẩm thành công'], 200);
        } catch (\Exception $e) {
            return response($e, 500);
        }
    }

    public function getSanPhamCaNhan()
    {
        $user = auth()->user();
        $sanPham = SanPham::where('user_id', $user->id)->get();
        return response($sanPham, 200);
    }

    public function editSanPham($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_san_pham' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu thông tin'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            SanPham::find($id)->update([
                'ten_san_pham' => $data['ten_san_pham'],
                'mo_ta' => $data['mo_ta'],
                'gia_ban' => $data['gia_ban'],
                'gia_nhap' => $data['gia_nhap'],
                'anh_dai_dien' => $data['anh_dai_dien'],
            ]);
            return response(['message' => 'Cập nhật sản phẩm thành công'], 200);
        } catch (\Exception $e) {
            return response($e, 500);
        }
    }

    public function xoaSanPham($id){
        try{
            $user = auth()->user();
            $sanPham = SanPham::where('id', $id)->first();
            if($user->id != $sanPham->user_id && $user->quyen_id == 1){
                return response(['message' => 'Không có quyền xóa sản phẩm'], 400);
            }else {
                $sanPham->delete();
                return response(['message' => 'Thành công'], 200);
            }
        }catch(\Exception $e){
            return response($e, 500);
        }
    }
}
