# システム固有の値を表現する「値オブジェクト」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## 値オブジェクトとは

値オブジェクトは、ドメインオブジェクトの中でもライフサイクルの無いオブジェクトです。(例えばPythonなどの)イミュータブルなオブジェクトにイメージが近いと思っています。例えば、"1"という整数値そのものは値を変更することができません。すなわち、"1"という値は永遠に"1"であるという意味において、ライフサイクルが存在しません。その意味で、Chapter3のエンティティと比較されます。

## Laravelにおける値オブジェクトの実装方針

まずはValue Objectを表すクラスを作成しますが、Laravelのbuilt-inの機能では、Value Objectを作成する機能が備わっていません。ただし、すでにLaravelで[値オブジェクトの作成を簡単に行うパッケージ](https://github.com/michael-rubel/laravel-value-objects)を作成している方がいますので、それをありがたく利用させてもらいます。

ただし、値オブジェクトのクラスの作成だけでは不十分です。
値オブジェクトはChapter3のエンティティ、すなわちLaravelのEloquent ORMで使用されることが想定されます。
そして、Eloquent ORMでは、データベースの保存もその責務としています。
従って、値オブジェクトがデータベースへ保存される際の挙動も、値オブジェクトのクラス作成と共に行なっておくと良いです。
具体的には、値オブジェクトがどのようにデータベースに保存されるか(例えばフルネームを表す値オブジェクトであればファーストネームとラストネームは別々のカラムに保存するのか？等)を定義しておきます。
これは、Laravelの[Custom Casts](https://laravel.com/docs/10.x/eloquent-mutators#custom-casts)を使用することによって簡潔に実装することができます。

## 値オブジェクトの作成

まずは値オブジェクトを作成していきます。
先述の通り、[Laravel Value Objects](https://github.com/michael-rubel/laravel-value-objects)を使用するため、まずはパッケージをインストールします。

```bash
composer require michael-rubel/laravel-value-objects
```

次に、[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)Chapter6で使用されるリスト6.2を参考に、UserName、UserIdクラスを作成します。

### UserName 値オブジェクトの作成

以下のコマンドでUserNameクラスを作成します。

```bash
php artisan make:value-object UserName
```

`app/ValueObjects/UserName.php`に雛形が作られていると思いますので、リスト6.2を参考に実装していきます。

```php:app/ValueObjects/UserName.php
<?php

declare(strict_types=1);

namespace App\ValueObjects;

use MichaelRubel\ValueObjects\ValueObject;
use Illuminate\Support\Facades\Validator;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @method static static make(mixed ...$values)
 * @method static static from(mixed ...$values)
 *
 * @extends ValueObject<TKey, TValue>
 */
class UserName extends ValueObject
{
    private string $username;

    protected $rules = [
        'username' => 'required|min:3|max:20'
    ];

    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct(string $username)
    {
        $this->username = $username;
        $this->validate();
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->username;
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "username" => $this->username
        ];
    }

    /**
     * Get string representation of the value object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * Validate the value object data.
     *
     * @return void
     */
    protected function validate(): void
    {
        $validator = Validator::make(
            $this->toArray(),
            $this->rules
        );
        $validator->validate();
    }
}

```

コードを見ていきましょう。

`declare(strict_types=1);`では、[PHPのStrict typing](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.strict)を指定しています。
これにより、引数や戻り値に指定した型と矛盾するコードに対して、[TypeError](https://www.php.net/manual/en/class.typeerror.php)が投げられます。

基本に忠実に実装していますが、注目したいのは`validate`メソッドです。
Laravelでは[Validator](https://laravel.com/docs/10.x/validation)を使用して値のチェックを行うと楽です。
上記のように`'username' => 'required|min:3|max:20'`などとruleを記載してあげることにより、簡潔な実装が可能になります。

正しく動作するか簡単にチェックしてみます(ちゃんとしたテストは先のChapter5に記載があるので、ここでは変数をdumpさせてテストします)。

`web.php`を以下のように書き換え、`php artisan serve`でサーバーを立ち上げてアクセスしてみます。

```php:routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use App\ValueObjects\UserName;

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
    try {
        $username = new UserName('');
    } catch (Exception $e) {
        dd($e->getMessage());
    }
    return view('welcome');
});
```

すると、`"The username field is required." // routes/web.php:21`が表示され、UserNameが空であったためにエラーが投げられていることがわかります。これは、ruleのところに`required`を記載していたからです。
`UserName('R')`に変更して同様にチェックすると、`"The username field must be at least 3 characters." // routes/web.php:21`が出力されており、最低文字数未満の場合にエラーが出力されていることが確認できます。

### UserId 値オブジェクトの作成

同様にUserIdの値オブジェクトを、後々必要となる[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)リスト6.2を参考に作成していきましょう。

```bash
php artisan make:value-object UserId
```

```php:app/ValueObjects/UserId.php
<?php

declare(strict_types=1);

namespace App\ValueObjects;

use MichaelRubel\ValueObjects\ValueObject;
use Illuminate\Support\Facades\Validator;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @method static static make(mixed ...$values)
 * @method static static from(mixed ...$values)
 *
 * @extends ValueObject<TKey, TValue>
 */
class UserId extends ValueObject
{
    private string $userId;

    protected $rules = [
        "userId" => "required"
    ];

    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
        $this->validate();
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->userId;
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "userId" => $this->userId
        ];
    }

    /**
     * Get string representation of the value object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->userId;
    }

    /**
     * Validate the value object data.
     *
     * @return void
     */
    protected function validate(): void
    {
        $validator = Validator::make(
            $this->toArray(),
            $this->rules
        );
        $validator->validate();
    }
}

```

## Custom Castの作成

次にUserNameとUserId用のCastクラスを作成していきます。
まずは、そもそもCastとは何かについて説明します。

### LaravelのCustom Castsとは？

LaravelのCustom Castsを用いると、Eloquent Modelのattributeに対して型を指定することができます。
[ドキュメントの例](https://laravel.com/docs/10.x/eloquent-mutators#custom-casts)を参考に、使用例を見てみましょう。

```php:app/Casts/Json
<?php
 
namespace App\Casts;
 
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
 
class Json implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        return json_decode($value, true);
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return json_encode($value);
    }
}
```

```php:app/Models/User.php
<?php
 
namespace App\Models;
 
use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
 
class User extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => Json::class,
    ];
}
```

上記のように、Custom CastsはCastsAttributesを継承し、getおよびsetメソッドを実装する必要があります。
getはデータベースの生値を変換する役割を、setは引数をデータベースの生値に変換する役割を持ちます。
作成したCustom Castのクラスを、使用するModelのcastsプロパティに渡してあげると、Custom Castクラスが使用できるようになります。

なお、Custom Castの実装は[Value Objectクラス内に入れることも可能](https://laravel.com/docs/10.x/eloquent-mutators#value-object-casting)です。
しかしながら、データベースへの保存方法の定義はValue Objectの責務ではないと考え、本記事では別クラスで定義することにします。

### UserNameのCustom Castの作成

以下のコマンドで、UserName Castを作成できます。

```bash
php artisan make:cast UserName
```

雛形が作成されていますので、修正していきます。

```php:app/Casts/UserName.php
<?php

namespace App\Casts;

use App\ValueObjects\UserName as UserNameValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class UserName implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return UserNameValueObject($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof UserNameValueObject) {
            throw new InvalidArgumentException(
                'The given value is not a UserName Object.'
            );
        }

        return [
            'username' => $value->toString()
        ];
    }
}


```

`use App\ValueObjects\UserName as UserNameValueObject;`では、Castクラスと値オブジェクトクラスが同じ名前なので、名前を変えてインポートしています。

まず、下のset関数を見てみます。
データベースに保存される前に、`$value`の型がUserNameであることをチェックし、予期せぬ動作を防止します。
そして、returnの部分で、`username`というDBのカラムにUserNameオブジェクトの値を保存するようにしています。
なお、ここでは`username`はDBにstring型で保存されることを想定しています([Chapter3で実際にschemaを定義します](https://qiita.com/reirev2913/items/eb6208985dda45cc58b8))。

次に上のget関数では、DBから取り出した値が扱いやすくなるようUserNameオブジェクトへの変換を行なっています。
動作確認は、[Chapter3](https://qiita.com/reirev2913/items/eb6208985dda45cc58b8)で行なっていきます。

### UserIdのCustom Castの作成

以下のコマンドで、UserId Castを作成できます。

```bash
php artisan make:cast UserId
```

先ほどと同様に、[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)リスト6.2を参考にUserIdクラスを作成します。

```php:app/Casts/UserId.php
<?php

namespace App\Casts;

use App\ValueObjects\UserId as UserIdValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class UserId implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return UserIdValueObject($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof UserIdValueObject) {
            throw new InvalidArgumentException(
                'The given value is not a UserId Object.'
            );
        }

        return [
            'user_id' => $value->toString()
        ];
    }
}

```

## まとめ

本記事では、UserNameとUserIdの値オブジェクトを作成し、さらにLaravelのCustom Castを作成して、データベースへの保存とデータベースからの取り出しの動作を定義しました。
これらを使ったUserモデルの作成は[Chapter3](https://qiita.com/reirev2913/items/eb6208985dda45cc58b8)で行っていきましょう。
