<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoProfileResource;
use App\Models\UsersPhotoProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoProfileController extends Controller
{
    public function index() {
        $photoProfiles = UsersPhotoProfile::with('user:id,name')->latest()->get();

        return new PhotoProfileResource(true, 'success', $photoProfiles);
    }

    public function show($id) {
        $photoProfile = UsersPhotoProfile::where('id', $id)->with('user')->first();

        if ($photoProfile) {
            return new PhotoProfileResource(true, 'Photo Profile Detail Sucessfully', $photoProfile);
        }

        return new PhotoProfileResource(false, 'Photo Profile Not Found!', null);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'photo_profile' => 'image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $photoProfile = $request->file('photo_profile');

        $photoProfile->storeAs('public/photoProfiles', Str::slug($user->name).'-'.$photoProfile->hashName());

        $photoProfile = UsersPhotoProfile::create([
            'photo_profile' => Str::slug($user->name).'-'.$photoProfile->hashName(),
            'user_id' => auth()->guard('api')->user()->id,
        ]);

        return new PhotoProfileResource(true, 'Photo Profile Sucessfull Created!',$photoProfile);

    }

    public function update(Request $request, UsersPhotoProfile $photo) {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'photo_profile' => 'required|image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('photo_profile')) {
            $photoProfile = $request->file('photo_profile');

            Storage::disk('local')->delete('public/photoProfiles/' . basename($photo->photo_profile));

            $photoProfilePath = $photoProfile->storeAs('public/photoProfiles', Str::slug($user->name) . '-' . $photoProfile->hashName());

            $photo->update([
                'photo_profile' => $photoProfilePath,
                'user_id' => auth()->guard('api')->user()->id,
            ]);

            return new PhotoProfileResource(true, 'Photo Profile Successfully Updated!', $photo);
        } else {
            return response()->json('No file uploaded', 422);
        }
    }


    public function destroy(UsersPhotoProfile $photo) {
               //remove image
               Storage::disk('local')->delete('public/photoProfiles/' . basename($photo->photo_profile));

               if ($photo->delete()) {
                   //return success with Api Resource
                   return new PhotoProfileResource(true, 'Photo Profile Sucessfull Deleted!', null);
               }

               //return failed with Api Resource
               return new PhotoProfileResource(false, 'Photo Profile Unsuccess Deleted!', null);
    }


}
