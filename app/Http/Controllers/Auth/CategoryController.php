<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = 'No Category Found.';
        $status = 0;
        $categories = Category::get();
        if(!empty($categories)){
            $status = 1;
            $message = 'Category lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'categoriesList' => $categories
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $createUser = Auth::user();
        $message = 'Category creation failed.';
        $status = 0;
        $request->validate([
            'category_name' => 'required|string'
        ]);

        $category = new Category;
        $category->category_name = $request->category_name;
        
        $category->save();
        
        if(!empty($category)){
            $message = 'Successfully created category!';
            $status = 1;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'category' => $category
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = 'Mentioned Category Not Found.';
        $status = 0;
        $categories = Category::find($id);
        if(!empty($categories)){
            $status = 1;
            $message = 'Category Details.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'categoryDetails' => $categories
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $createUser = Auth::user();
        $message = 'Category update failed.';
        $status = 0;
        $request->validate([
            'category_name' => 'required|string'
        ]);

        $category = Category::find($id);
        $category->category_name = $request->category_name;
        $category->save();
        if(!empty($category)){
            $message = 'Successfully updated category!';
            $status = 1;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'category' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = 'Failed delete Category.';
        $status = 0;
        $category = Category::find($id);
        if($category){
            $category->delete();
            $message = 'Category deleted successfully.';
            $status = 1;
        }else{
            $message = 'Category does not available.';
            $status = 0;
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }
}
