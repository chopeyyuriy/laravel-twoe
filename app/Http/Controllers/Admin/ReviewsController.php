<?php

namespace App\Http\Controllers\Admin;

use App\Reviews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewsController extends Controller
{
    public function index()
    {
        $all_reviews = Reviews::leftJoin('users', 'reviews.from_user_id', 'users.id')
            ->select('reviews.*', 'users.avatar', 'users.first_name', 'users.last_name', 'users.login')
            ->get();
        return view('admin.reviews.index', ['all_reviews' => $all_reviews]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $reviews = Reviews::leftJoin('users', 'reviews.from_user_id', 'users.id')
            ->select('reviews.*', 'users.avatar', 'users.first_name', 'users.last_name', 'users.login')
            ->where('reviews.from_user_id', $id)
            ->get();
        return response()->json(['reviews' => $reviews]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $reviews = Reviews::findOrFail($id);
        $reviews->delete();
        return redirect('/admin/reviews')->with('success', 'Отзив удален !');
    }
}
