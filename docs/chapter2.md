# システム固有の値を表現する「値オブジェクト」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## 値オブジェクトの作成

### Laravel Value Objectsのインストール

すでにLaravelで値オブジェクトの作成を簡単に行う[パッケージ](https://github.com/michael-rubel/laravel-value-objects)を作成している方がいますので、それをありがたく利用させてもらいます。
以下でパッケージをインストールします。

```[bash]
composer require michael-rubel/laravel-value-objects
```

### FullNameクラスの作成

書籍に倣い、FullNameクラスの実装を行なっていきます。

先ほどインストールしたパッケージを使用し、FullNameクラスを作成します。

```[bash]
php artisan make:value-object FullName
```

`app/ValueObjects/FullName.php`にFullNameクラスが以下のように実装されているはずです。

```php:app/ValueObjects/FullName.php
<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Validation\ValidationException;
use MichaelRubel\ValueObjects\ValueObject;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @method static static make(mixed ...$values)
 * @method static static from(mixed ...$values)
 *
 * @extends ValueObject<TKey, TValue>
 */
class FullName extends ValueObject
{
    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct()
    {
        $this->validate();
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        // TODO: Implement value() method.
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        // TODO: Implement value() method.
    }

    /**
     * Get string representation of the value object.
     *
     * @return string
     */
    public function __toString(): string
    {
        // TODO: Implement value() method.
    }

    /**
     * Validate the value object data.
     *
     * @return void
     */
    protected function validate(): void
    {
        // TODO: Implement validate() method.

        if (empty($this->value())) {
            throw ValidationException::withMessages([__('Value of FullName cannot be empty.')]);
        }
    }
}

```

`declare(strict_types=1);`では、[PHPのStrict typing](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.strict)を指定しています。
これにより、引数や戻り値に指定した型と矛盾するコードに対して、[TypeError](https://www.php.net/manual/en/class.typeerror.php)が投げられます。

[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)リスト2.18のように実装すると、以下のようになります。

```php:app/ValueObjects/FullName.php
class FullName extends ValueObject
{
    private string $firstName;
    private string $lastName;

    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->validate();
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        // TODO: Implement value() method.
        return $this->firstName .  ' ' . $this->lastName;
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName(),
            'lastName'  => $this->lastName(),
        ];
    }

    /**
     * Validate the value object data.
     *
     * @return void
     */
    protected function validate(): void
    {
        // TODO: Implement validate() method.

        if (empty($this->value())) {
            throw ValidationException::withMessages([__('Value of FullName cannot be empty.')]);
        }
    }
}

```

なお、FullNameクラスが継承しているValueObjectクラスで、equalsメソッドがすでに実装されているので、改めてequalsメソッドを実装する必要はありません。

```php:vendor/michael-rubel/laravel-value-objects/src/ValueObject.php
public function equals(ValueObject $object): bool
{
    return $this == $object;
}
```

## 実装の確認

本来はテストコードを書くのが良いですが、テストを書く章は別に存在していますので、ここでは簡単に実装ができているかどうかを確認してみます。

web.phpで以下のように記載し、`php artisan serve`でサーバーを起動して、ブラウザでアクセスします。

```php:/var/www/html/routes/web.php
Route::get('/', function () {
    $name = new FullName('Rei', 'Rev');
    dd($name);
    return view('welcome');
});
```

以下のように表示され、正しく実装できていそうなことが確認できます。

```browser
App\ValueObjects\FullName {#300 ▼ // routes/web.php:19
  -firstName: "Rei"
  -lastName: "Rev"
}
```

さらに、FullNameオブジェクトの等価性を確認しましょう。

```php:/var/www/html/routes/web.php
Route::get('/', function () {
    $name1 = new FullName('Rei', 'Rev');
    $name2 = new FullName('Rei', 'Rev');
    $name3 = new FullName('Foo', 'Bar');
    dd(
        $name1,
        $name1->equals($name2),
        $name1->equals($name3),
        $name1 == 'Rei Rev',
        $name1 == $name2,
        $name1 == $name3,
        $name1 === $name2,
        $name1 === $name3
    );
    return view('welcome');
});
```

```browser
App\ValueObjects\FullName {#300 ▼ // routes/web.php:21
  -firstName: "Rei"
  -lastName: "Rev"
}
true // routes/web.php:21
false // routes/web.php:21
true // routes/web.php:21
true // routes/web.php:21
false // routes/web.php:21
false // routes/web.php:21
false // routes/web.php:21
```

PHPの仕様上、通常の比較演算子では、`FullName('Rei', 'Rev')`と`'Rei Rev'`の`==`での比較はtrueを返してしまう。
そのため、Laravel Value Objectsパッケージでは、equalsメソッドが用意されている。
比較する際には、equalsメソッドを使用するようにする。
