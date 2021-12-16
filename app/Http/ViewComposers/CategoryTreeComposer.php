<?php
namespace App\Http\ViewComposers;

use App\Models\Product\Category;
use Illuminate\View\View;

class CategoryTreeComposer
{
    // 当渲染指定的模板时，Laravel 会调用 compose 方法
    public function compose(View $view)
    {
        // 使用 with 方法注入变量
        $view->with('categoryTree', Category::getCategoryTree());
    }
}
