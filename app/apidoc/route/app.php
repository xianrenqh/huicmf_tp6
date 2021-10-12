<?php

use think\facade\Route;

Route::rule('config', 'apidoc/index/getConfig');
Route::rule('data', 'apidoc/index/getList');
Route::rule('auth', 'apidoc/index/verifyAuth');