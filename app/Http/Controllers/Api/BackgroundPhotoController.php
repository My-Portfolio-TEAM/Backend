<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BackgroundPhotoResource;
use App\Models\UsersBackgroundPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BackgroundPhotoController extends Controller
{
    public function index()
    {
        $backgroundPhotos = UsersBackgroundPhoto::with('user:id,name')->latest()->get();

        return new BackgroundPhotoResource(true, 'success', $backgroundPhotos);
    }

    public function show($id)
    {
        $backgroundPhotos = UsersBackgroundPhoto::where('id', $id)->with('user')->first();

        if ($backgroundPhotos) {
            return new BackgroundPhotoResource(true, 'Background Photo Detail Sucessfully', $backgroundPhotos);
        }

        return new BackgroundPhotoResource(false, 'Background Photo Not Found!', null);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'background_photo' => 'image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $backgroundPhoto = $request->file('background_photo');

        $backgroundPhoto->storeAs('public/backgroundPhotos', 'background-' . Str::slug($user->name) . '-' . $backgroundPhoto->hashName());

        $backgroundPhoto = UsersBackgroundPhoto::create([
            'background_photo' => 'background-' . Str::slug($user->name) . '-' . $backgroundPhoto->hashName(),
            'user_id' => auth()->guard('api')->user()->id,
        ]);

        return new BackgroundPhotoResource(true, 'Background Photo Sucessfull Created!', $backgroundPhoto);
    }

    public function update(Request $request, UsersBackgroundPhoto $background)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'background_photo' => 'image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('background_photo')) {
            $backgroundPhoto = $request->file('background_photo');

            Storage::disk('local')->delete('public/backgroundPhotos/' . basename($background->background_photo));

            $backgroundPhotoPath = $backgroundPhoto->storeAs('public/backgroundPhotos', 'background-' . Str::slug($user->name) . '-' . $backgroundPhoto->hashName());

            $background->update([
                'background_photo' => $backgroundPhotoPath,
                'user_id' => auth()->guard('api')->user()->id,
            ]);

            return new BackgroundPhotoResource(true, 'Background Photo Successfully Updated!', $background);
        } else {
            return response()->json('No file uploaded', 422);
        }
    }


    public function destroy(UsersBackgroundPhoto $background)
    {
        //remove image
        Storage::disk('local')->delete('public/backgroundPhotos/' . basename($background->background_photo));

        if ($background->delete()) {
            //return success with Api Resource
            return new BackgroundPhotoResource(true, 'Background Photo Sucessfull Deleted!', null);
        }

        //return failed with Api Resource
        return new BackgroundPhotoResource(false, 'Background Photo Unsuccess Deleted!', null);
    }
}
