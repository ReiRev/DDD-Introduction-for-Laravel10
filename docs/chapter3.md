# Chapter 3: ライフサイクルのあるオブジェクト「エンティティ」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## Laravelにおけるエンティティの作成方針

[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)によれば、エンティティの性質は以下の通りです。

- 可変である
- 同じ属性であっても区別される
- 同一性により区別される

また、ライフサイクルのあるオブジェクトという説明もあります。
これらの意味で、エンティティは値オブジェクトと区別されます。

Laravelでは、[Eloquent](https://laravel.com/docs/10.x/eloquent)が採用されています。
これにより、エンティティクラスでデータベースに関する操作も定義され、面倒なデータベース操作が楽になります。
従って、Eloquentを使用して基本に忠実にエンティティを実装すれば良いです。

### Userエンティティの作成

[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)Chapter6で使用されるリスト6.1のUserクラスを作成していきます。

あらかじめ、`app/models/User.php`と、`database/migrations/*_create_user_table.php`を削除しておきます。
Userクラスとデータベースのtableのスキーマを作成しましょう。

```bash
php artisan make:model User --migration
```

`app/Models/User.php`と`database/migrations/2023_06_25_033245_create_users_table.php`が作成されたと思います。
後者のファイル名には作成日が反映されているはずです。
`User.php`ではUserクラスを記述しますが、`*_create_users_table.php`では、データベースのスキーマを定義します。
まずは、`User.php`から実装し、その後に`*_create_users_table.php`を記述していきましょう。

### Userエンティティの実装

Userエンティティの実装は以下のようになります。

```php:app/Models/User.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Casts\UserName as UserNameCast;

class User extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'username'
    ];

    protected $casts = [
        'username' => UserNameCast::class
    ];
}

```

[Chapter 2](https://qiita.com/reirev2913/items/1ca692571ca6ffb2f1f9)では、UserIdの値オブジェクトを作成しましたが、きちんと独自のIdを作成するのは大変ですので、ここでは[Laravel built-inのUUID](https://laravel.com/docs/10.x/eloquent#uuid-and-ulid-keys)を使いましょう。
LaravelのEloquentモデルでは、デフォルトではint型のidが使用され、自動でインクリメントされるようになっています。
代わりにstring型のUUIDを使用したい場合には、`use HasUuids;`をクラス内に記述することで、UUIDが使用できるようになります。
とても便利ですね。

次に、[Chapter 2](https://qiita.com/reirev2913/items/1ca692571ca6ffb2f1f9)で作成したUserName値オブジェクトをUserクラスがアトリビュートとして持てるようにします。
Laravelでは、`fillable`にModelが持つプロパティを記述していきます。
ここでは、UserエンティティはUserName値オブジェクトを持つので、`fillable`に`username`を追加します。
ここで`username`はDBのカラム名に対応します。
`username`はUserName値オブジェクトとして渡されて欲しいので、[Chapter 2](https://qiita.com/reirev2913/items/1ca692571ca6ffb2f1f9)で作成したUserNameクラスを`casts`に渡します。

なお、Laravelでは`fillable`の他に`guarded`もあります。
`fillable`に指定したアトリビュートは、後述の`create`メソッドなどで指定した際にオブジェクトに反映されますが、`guarded`に指定したものは無視され、オブジェクトに反映されません。
デフォルトでは`guareded=['*']`となっており、全てが`guarded`に指定されます。
従って、`id`は`guarded`として指定され、createなどで任意に代入できないようになっているので、そのままにしておきます。

### schemaの作成

以下のようにschemaを作成します。

```php:database/migrations/2023_06_25_033245_create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

```

前節でidとしてUUIDを使用することにしたので、その部分の指定を行っています。
また、usernameをstring型として保存するカラムも指定しています。
[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)では、`username`をuniqueなもの仮定しているので、DBの方でもそのように指定しておきます。

### 動作確認

ちゃんとしたテストの作成は[Chapter 5](https://qiita.com/reirev2913/items/fb514d200e692d805e17)で出てきますので、ここでは簡単に動作確認をしてみます。

まず、DBのschemaに変更を加えたので、それを反映させます。

```bash
php artisan migrate:fresh
```

以下で、usersのtableがどうなっているか確認してみましょう。

```bash
php artisan db:table users
```

```txt:出力
users ..............................................................................................  
Columns .......................................................................................... 4  
Size ....................................................................................... 0.02MiB  

Column ........................................................................................ Type  
id string ................................................................................... string  
username string ............................................................................. string  
created_at datetime, nullable ............................................................. datetime  
updated_at datetime, nullable ............................................................. datetime  

Index ..............................................................................................  
PRIMARY id ......................................................................... unique, primary  
users_username_unique username .............................................................. unique 
```

上記から、idとusrnameカラムが問題なく作成されていることが分かります。
親切にも、created_atとupdated_atのカラムも作成されています。
これらは一般的なDBの使用で有意義なものですので、そのまま残しておきましょう。

`web.php`を以下のように変更し、簡単な動作確認をしてみます。

```php:routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use App\ValueObjects\UserName;
use App\Models\User;

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
    $username = new UserName("ReiRev");
    $user = User::create(
        [
            "username" => $username,
            'id' => 'hoge'
        ]
    );
    $users = User::all();
    foreach ($users as $user) {
        dump($user);
    }
    return view('welcome');
});

```

`php artisan serve`でサーバーを起動し、localhostを開くと、問題なくUserが作成されていることが確認できると思います。
ここで、idも指定してcreateしていますが、実際のidはUUIDになっているはずです。
これは後述した通り、idがguardedになっており、createでは代入できるようになっているからです。
ブラウザをリロードして見ると、以下のようにエラーが表示され、usernameがuniqueなカラムになっていることが確認できます。

```bash
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'ReiRev' for key 'users.users_username_unique' 
```

## まとめ

本記事では、LaravelのEloquentを使用してエンティティの作成を行いました。
Eloquentを用いることで、少ないコード量で実装を行うことができますので、有効活用していきましょう。
