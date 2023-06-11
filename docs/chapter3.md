# Chapter 3: ライフサイクルのあるオブジェクト「エンティティ」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## Userモデルの作成

作成したプロジェクトのテンプレートには、あらかじめUserモデルが`/var/www/html/app/Models/User.php`に作成されていますので、まずはこれを削除します。
また、`database/migrations/2014_10_12_000000_create_users_table.php`も消しておきます。

その後、以下でUserモデルを作成しましょう。

```bash
php artisan make:model User --migration
```

`app/Models/User.php`にUserモデルが作成されたはずです。

以下でマイグレーションを行います。

```bash
php artisan migrate:fresh
php artisan migrate
```

以下で、データベースにモデルが作成されていることが確認できます。

```bash
php artisan db:table users
```

```bash
users ...............................................................................................................................  
Columns ........................................................................................................................... 3  
Size ........................................................................................................................ 0.02MiB  

Column ......................................................................................................................... Type  
id autoincrement, bigint, unsigned ........................................................................................... bigint  
created_at datetime, nullable .............................................................................................. datetime  
updated_at datetime, nullable .............................................................................................. datetime  

Index ...............................................................................................................................  
PRIMARY id .......................................................................................................... unique, primary
```

LaravelのEloquentモデルでは、[primary keyは自動でインクリメントされます](https://laravel.com/docs/10.x/eloquent#primary-keys)。
また、[デフォルトで、created_at及びupdated_atキーが作成](https://laravel.com/docs/10.x/eloquent#timestamps)されます。

## nameプロパティの追加

Eloquentモデルの場合、以下のようにfillableプロパティを追加するだけで、nameプロパティを追加できます。

```php:app/Models/User.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}

```

少し先取りになってしまいますが、データベースのスキーマも変更しておきます。
具体的には、nameカラムが文字列であることを指定しておきます。

```php:database/migrations/2023_06_11_071252_create_users_table.php
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
            $table->id();
            $table->timestamps();
            $table->string('name');
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

以下のようにモデルを作成して、問題なく動作することを確認しましょう。

```bash
php artisan migrate:reset
php artisan migrate:fresh
```

```php:routes/web.php
<?php

use Illuminate\Support\Facades\Route;
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
    $user = User::create(['name' => 'Rei Rev']);
    dd($user);
    return view('welcome');
});

```

以下のようにブラウザで確認でき、適切にUserモデルが作成されていることを確認できます。

```browser
App\Models\User {#309 ▼ // routes/web.php:19
  #connection: "mysql"
  #table: "users"
  #primaryKey: "id"
  #keyType: "int"
  +incrementing: true
  #with: []
  #withCount: []
  +preventsLazyLoading: false
  #perPage: 15
  +exists: true
  +wasRecentlyCreated: true
  #escapeWhenCastingToString: false
  #attributes: array:4 [▶
    "name" => "Rei Rev"
    "updated_at" => "2023-06-11 07:40:18"
    "created_at" => "2023-06-11 07:40:18"
    "id" => 2
  ]
  #original: array:4 [▶
    "name" => "Rei Rev"
    "updated_at" => "2023-06-11 07:40:18"
    "created_at" => "2023-06-11 07:40:18"
    "id" => 2
  ]
  #changes: []
  #casts: []
  #classCastCache: []
  #attributeCastCache: []
  #dateFormat: null
  #appends: []
  #dispatchesEvents: []
  #observables: []
  #relations: []
  #touches: []
  +timestamps: true
  +usesUniqueIds: false
  #hidden: []
  #visible: []
  #fillable: array:1 [▶
    0 => "name"
  ]
  #guarded: array:1 [▶
    0 => "*"
  ]
}
```

## 比較メソッドの実装

[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)リスト3.6にならい、比較メソッドを実装しておきます。
書籍に書かれているように、idによって2つのオブジェクトを比較するようにします。

```php:app/Models/User.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function equals(User $user): bool
    {
        return this->id == $user->id;
    }
}
```

これで、書籍の内容は実装完了です。
