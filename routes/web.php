<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/top','CmsControllers@index');
// userlist表示
Route::get('/users','CmsControllers@user_list');
// 新規登録
Route::get('/create/user','CmsControllers@user_add')->name('create_user');
Route::get('/seach/user/{name}','CmsControllers@getUsersBySearchName');
Route::get('/seach/pass','CmsControllers@getPassBySearchName');
// DB登録
Route::post('/store/user','CmsControllers@user_store');
// userDelete
Route::post('/delete/user','CmsControllers@user_delete');
// userModifyの表示
Route::get('/modify/user','CmsControllers@user_modify');
// userUpdate
Route::post('/update/user','CmsControllers@user_update');

// contentslistの表示
Route::get('/contents','CmsControllers@contents_list');
// 新規登録
Route::get('/create/content','CmsControllers@content_add')->name('create_content');
// DB登録
Route::post('/store/content','CmsControllers@content_store');
// contentDelete
Route::post('/delete/content','CmsControllers@content_delete');
// contentModify
Route::get('/modify/content','CmsControllers@content_modify');
// contentUpdate
Route::post('/update/content','CmsControllers@content_update');

// schedulelistの表示
Route::get('/schedule','CmsControllers@schedule_list');
Route::post('/get/content','CmsControllers@get_content');
Route::post('/schedule/display','CmsControllers@schedule_display');
// 新規登録
Route::get('/create/schedule','CmsControllers@schedule_add');
// DB登録
Route::post('/store/schedule','CmsControllers@schedule_store');
// scheduleDelete
Route::post('/delete/schedule','CmsControllers@schedule_delete');
// scheduleModify
Route::get('/modify/schedule','CmsControllers@schedule_modify');
// scheduleUpdate
Route::post('/update/schedule','CmsControllers@schedule_update');
// scheduleUpdate
Route::post('/update/c_schedule','CmsControllers@schedule_content_update');

Route::get('/map','CmsControllers@gmap');
Route::get('/ride/map','CmsControllers@ride_map');


Route::get('/normal','CmsControllers@normal');