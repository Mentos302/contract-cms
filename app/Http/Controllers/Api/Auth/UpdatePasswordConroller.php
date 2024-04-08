<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UpdatePasswordConroller extends Controller
{
    public function updatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'password' => ['required', 'string', 'min:8'],
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $rtn = [
                'errors' => $validator->errors()
            ];
            return response()->json($rtn, 422);
        }

        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        if (!isset($request->old_password) || !Hash::check($request->old_password, $user->password)) {
            $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            $rtn = [
                'errors' => $validator->errors()
            ];
            return response()->json($rtn, 422);
        }
        $user->password = Hash::make($request['password']);

        $user->save();
        $rtn = [
            'message' => 'Password Updated Successfully'
        ];
        return response()->json($rtn, 201);
    }
}
