<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function index() {
        $users = User::with('photoProfile', 'biodata')->latest()->latest()->paginate(10);

        return new UserResource(true, 'List Users', $users);

    }

    public function show($id) {
        $user = User::where('id', $id)->with('photoProfile', 'backgroundPhoto', 'biodata', 'portfolios', 'posts')->first();

        if ($user) {
            return new UserResource(true, 'User Detail Sucessfully', $user);
        }

        return new UserResource(false, 'User Not Found!', null);
    }

    public function profile() {
        $userId = Auth::id();

        $me = User::where('id', $userId)->with('photoProfile', 'backgroundPhoto', 'biodata', 'portfolios', 'posts')->first();

        return new UserResource(true, 'Get My Own Profile Success', $me);

    }

    public function update(Request $request) {

        $userId = Auth::id();
        $user =  User::where('id', $userId)->first();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'min:5|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if($request->password == "") {

            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);

        } else {

            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);

        }

        if($user) {
            return new UserResource(true, "$user->name Successfull Updated", $user);
        }

        return new UserResource(false, "Update Failed", null);

    }

    public function delete(User $user) {
        if($user->delete()) {
            return new UserResource(true, 'User Data Deleted!', null);
        }

        return new UserResource(false, 'Failed to Delete User Data!', null);

    }

}
