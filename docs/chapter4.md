# Chapter 4: 不自然さを解決する「ドメインサービス」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## UserServiceの作成

`app/Services`フォルダを作成し、`UserService.php`を作成します。
残念ながら現在は`php artisan`でServiceを作成するコマンドはないようです。

以下のようにサービスを作成します。

```php:app/Services/UserService.php
<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function exists(User $user)
    {
        return User::where('name', $user->name)->first() !== null;
    }
}
```

データベースに`name`が同じUserが存在するかどうかを確認しています。

## 動作確認

以下のようにweb.phpを変更し、簡単に動作確認をしてみます。
`php artisan serve`でサーバーを起動し、localhostにアクセスしてみます。

```php:routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Services\UserService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $reirev = new User(['name' => 'Rei Rev']);
    $reirev->save();

    $foo = new User(['name' => 'foo']);

    $userService = new UserService();
    dd(
        $userService->exists($reirev),
        $userService->exists($foo)
    );
    return view('welcome');
});

```

以下のように表示され、UserServiceが正しく動作していることを確認できます。

```browser
true // routes/web.php:25

false // routes/web.php:25
```
