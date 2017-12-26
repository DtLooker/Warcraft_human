<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//debug ?XDEBUG_SESSION_START=10870
//Banner  h.com/api/v1/banner/1
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

//Theme  h.com/api/v1/theme?ids=1,2,3
Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');

//Theme type   h.com/api/v1/theme/1
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexList');

//Product   h.com/api/v1/product?count=15
Route::get('api/:version/product', 'api/:version.Product/getRecent');
// Category--->Product    h.com/api/v1/product/by_category?id=4
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');

//Category  h.com/api/v1/category/all
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategory');

//Token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');