<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    // Send categories to navbar
    public function navbarCategories()
    {
        return Category::with('children:id,parent_id,name,slug')
            ->whereNull('parent_id')
            ->get();
    }
}
