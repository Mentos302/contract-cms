<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilePostRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class UserProfileConroller extends Controller
{
    public function userProfile() {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function userProfilePost(ProfilePostRequest $request)
    {
        $requestData = $request->validated();
        $user = User::findOrFail(auth()->user()->id);
        $user->update($requestData);
        return new UserResource($user);
    }

}
