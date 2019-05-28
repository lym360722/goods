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

Route::get('api/:version/banner/:id','api/:version.Banner/getBanner'); // 首页顶部banner轮播图

Route::get('api/:version/theme','api/:version.Theme/getSimpleList'); // 精品主题

Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');

Route::get('api/:version/product/recent','api/:version.Product/getRecent');// 最新新品
Route::get('api/:version/product/by_category','api/:version.Product/getAIIcategory');
Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);// 查询商品详情

//路由分组 性能会比较高 但是没有单独的路由直观
//Route::group('api/:version/product',function(){
//    Route::get('/recent','api/:version.Product/getRecent');
//    Route::get('/by_category','api/:version.Product/getAIIcategory');
//    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//});


Route::get('api/:version/category/all','api/:version.category/getAllCategory');

Route::post('api/:version/token/user','api/:version.Token/getToken'); // 获取Token

Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');//添加用户地址

Route::post('api/:version/order','api/:version.order/placeOrder');//下单
Route::get('api/:version/order/by_user','api/:version.order/getSummaryByUser'); // 我的订单
Route::get('api/:version/order/:id','api/:version.order/getDetail',[],['id'=>'\d+']); // 订单详情

Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');//支付

Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');//回调通知