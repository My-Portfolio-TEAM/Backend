<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BiodataResource;
use Illuminate\Support\Facades\Validator;
use App\Models\UsersBiodata;
use Illuminate\Http\Request;

class BiodataController extends Controller
{
    public function index() {
        $biodatas = UsersBiodata::with('user:id,name')->latest()->get();

        return new BiodataResource(true, 'List Biodata', $biodatas);
    }


    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'about' => 'required|string',
            'headline' => 'required|string',
            'role' => 'required|string',
            'location' => 'required|string',
            'skills' => 'required|array',
            'linkedIn' => 'url',
            'website' => 'url',

        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $biodata = UsersBiodata::create([
            'about' => $request->about,
            'headline' => $request->headline,
            'role' => $request->role,
            'location' => $request->location,
            'skills' => $request->skills,
            'linkedIn' => $request->linkedIn,
            'website' => $request->website,
            'user_id' => auth()->guard('api')->user()->id,
        ]);

        if($biodata) {
            return new BiodataResource(true, 'Biodata Created!', $biodata);
        }

        return new BiodataResource(false, 'Biodata Unsuccess Created!', null);

    }

    public function show($id) {
        $biodata = UsersBiodata::where('id', $id)->with('user')->first();

        if($biodata) {
            return new BiodataResource(true, 'Success',  $biodata);
        }

        return new BiodataResource(false, 'Biodata Not Found!',  null);

    }

    public function update(Request $request, UsersBiodata $biodata) {
        $validator = Validator::make($request->all(), [
            'about' => 'required|string',
            'headline' => 'required|string',
            'role' => 'required|string',
            'location' => 'required|string',
            'skills' => 'required|array',
            'linkedIn' => 'url',
            'website' => 'url',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $biodata->update([
            'about' => $request->about,
            'headline' => $request->headline,
            'role' => $request->role,
            'location' => $request->location,
            'skills' => $request->skills,
            'linkedIn' => $request->linkedIn,
            'website' => $request->website,
            'user_id' => auth()->guard('api')->user()->id,
        ]);

        if($biodata) {
            return new BiodataResource(true, 'Biodata Updated!', $biodata);
        }

        return new BiodataResource(false, 'Biodata Unsuccess Updated!', null);
    }

    public function destroy(UsersBiodata $biodata) {
        if($biodata->delete()) {
            return new BiodataResource(true, 'Biodata Sucessfull Deleted', null);
        }
        return new BiodataResource(false, 'Biodata Unsuccess Deleted', null);
    }
}
