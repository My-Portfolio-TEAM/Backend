<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use App\Models\UsersBiodata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    public function index() {
        $portfolios = Portfolio::with('user')->latest()->get();

        return new PortfolioResource(true, 'success', $portfolios);
    }

    public function show($id) {
        $portfolio = Portfolio::where('id', $id)->with('user')->first();

        if($portfolio) {
            return  new PortfolioResource(true, 'success',  $portfolio);
        }
        return response()->json([
            'status' => false,
            'message' => 'Portfolio Not Found!',
            'data' => null
        ], 404);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'link' => 'required|url',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/portfolios', $image->hashName());

        $portfolio = Portfolio::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image->hashName(),
            'link' => $request->link,
            'user_id' => auth()->guard('api')->user()->id,

        ]);

        if($portfolio) {
            return new PortfolioResource(true, 'Portfolio Successfull Created!', $portfolio);
        }
        return new PortfolioResource(false, 'Portfolio Unsuccessfull Created!', null);


    }

    public function update(Request $request, Portfolio $portfolio) {
        $validator = Validator::make($request->all(),  [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'link' => 'required|url',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if($request->file('image')) {
            Storage::disk('local')->delete('public/portfolios/'. basename($portfolio->image));

            $image = $request->file('image');
            $image->storeAs('public/portfolios', $image->hashName());

            $portfolio->update([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $image->hashName(),
                'link' => $request->link,
                'user_id' => auth()->guard('api')->user()->id,
            ]);
        }

        $portfolio->update([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'user_id' => auth()->guard('api')->user()->id,

        ]);

        if($portfolio) {
            return new PortfolioResource(true, 'Portfolio Successfull Edited!', $portfolio);
        }
        return new PortfolioResource(true, 'Portfolio Unsuccessfull Edited!', null);


    }

    public function destroy(Portfolio $portfolio) {
        Storage::disk('local')->delete('public/portfolios/'. basename($portfolio->image));

        if($portfolio->delete()) {
            return new PortfolioResource(true, 'Portfolio Sucessfull Deleted!', null);
        }
        return new PortfolioResource(true, 'Portfolio Unsucessfull Deleted!', null);
    }
}
