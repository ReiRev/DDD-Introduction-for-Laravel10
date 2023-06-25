# Chapter 5: データにまつわる処理を分離する「リポジトリ」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## リポジトリの作成はLaravelに必要か？

「[ドメイン駆動設計入門](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)」ではリポジトリを作成しています。
リポジトリの責務は「データの永続化と再構築」で、データベースの操作などを行います。
これらの操作を抽象化したクラスをリポジトリとして用意することで、例えばMySQLからSQLiteへの変更などが生じた際のソフトウェア全体でのコード変更を最小限にとどめることができるようになり、ソフトウェアに柔軟性をもたらします。

ところで、Laravelでアプリケーションを構築する際にはリポジトリクラスを作成する必要があるのでしょうか？
このような疑問が生じる背景には、Laravelが[Eloquent ORM](https://laravel.com/docs/10.x/eloquent)を採用しているためにデータベース操作が既に抽象化されているという事実があります。
Laravelでは、すでにEloquent ORMによってデータベース操作が抽象化されており、例えば開発中でMySQLからSQLiteへの変更など、データベースの変更があったとしてもコードの変更は不要です。
したがって、リポジトリの作成は不要でしょう。
同じような意見は[こちら](https://adelf.tech/2019/useless-eloquent-repositories)でも指摘されています。

## テスト用のデータベースを作成する

本説では、テスト用のデータベースを用意し、テストを行なっていきます。

### テストの実行方法

あらかじめ、web.phpの中身を元に戻しておきます(Exampleのテストで`/`へのroutingがテストされるため)。

```php:routes/web.php
Route::get('/', function () {
    return view('welcome');
});
```

データベースファイルなどを変更している場合には、以下でマイグレーションを行なっておきます。

```bash
php artisan test --migrate-configuration
```

以下で、もともと用意されている`tests/Feature/ExampleTest.php`と`tests/Unit/ExampleTest.php`が実行されます。

```bash
php artisan test
```

```bash:実行結果
PASS  Tests\Unit\ExampleTest
✓ that true is true                                                                         0.02s  

PASS  Tests\Feature\ExampleTest
✓ the application returns a successful response                                             0.57s  

Tests:    2 passed (2 assertions)
Duration: 0.82s
```

### SQLiteを使用したテスト環境のセットアップ

「[ドメイン駆動設計入門](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)」で示されているように、インメモリデータベースである[SQLite](https://www.sqlite.org/index.html)を使用してテストを行うための設定を行なっていきましょう。

まず、`phpunit.xml`を開き、`<env name="DB_DATABASE" value=":memory:"/>`と`<env name="DB_CONNECTION" value="sqlite"/>`を追加します。
`phpunit.xml`に作成された環境変数(`<env name="foo" value="bar"/>`等)は、[テスト実行時に読み込まれます](https://laravel.com/docs/10.x/testing#environment)。
ここで、`DB_DATABASE`の値を`:memory:`とすることによって、データベース用のファイルを作成せずにインメモリのデータベースでテストを動作させることができます。

```xml:phpunit.xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.2/phpunit.xsd" bootstrap="vendor/autoload.php" colors="true">
  <testsuites>
    <testsuite name="Unit">
      <directory suffix="Test.php">./tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
      <directory suffix="Test.php">./tests/Feature</directory>
    </testsuite>
  </testsuites>
  <coverage/>
  <php>
    <env name="APP_ENV" value="testing"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="TELESCOPE_ENABLED" value="false"/>
  </php>
  <source>
    <include>
      <directory suffix=".php">./app</directory>
    </include>
  </source>
</phpunit>
```

### UserName 値オブジェクトのテスト

[Chapter 2](https://qiita.com/reirev2913/items/1ca692571ca6ffb2f1f9)で作成したUserName 値オブジェクトの動作確認を簡易なもので済ませてしまっていたので、書籍に記載はありませんが、ここでしっかりとテストを作成しておきましょう。

以下でテストを作成します。

```bash
php artisan make:test UserNameTest
```

ここでは、テストをfeature testとして作成しています。
プロジェクトでデフォルトで作成されているように、Laravelのテストフォルダは`tests/Unit`と`tests/Feature`の2つが用意されています。
`make:test`で`-u`オプションをつけると、`tests/Unit`フォルダの方にテストが作成されますが、特に指定しない場合は`tests/Feature`フォルダの方に作成されます。
一般的に、ユニットテストでは1つのクラスのみをテスト対象とする場合に使用し、フィーチャーテストは複数のクラスが使用される複雑なテストを対象とする場合に使用します。
ここで、値オブジェクトは他のクラスやデータベースには依存しないと判断して、ユニットテストを作成すると問題が生じます。
Laravelのテストでは、Facade(Laravelの機能群)を使用する場合はfeature test、しない場合はunit testで使い分けます。
デフォルトではunit testでFacadeが使われない設定になっているため、Facadeを使用するか否かでテストディレクトリを分けた方がいいでしょう。
今回の値オブジェクトはFacadeを使用しているので、フィーチャーテストとして作成します。

作成された`tests/Feature/UserNameTest.php`を以下のように編集します。

```php:tests/Feature/UserNameTest.php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\ValueObjects\UserName;

use Exception;

class UserNameTest extends TestCase
{
    public function test_empty_input(): void
    {
        $this->expectException(Exception::class);
        new UserName('');
    }

    public function shortNames(): array
    {
        return [
            ['a'],
            ['ab']
        ];
    }

    /**
     * @dataProvider shortNames
     */
    public function test_short_name(string $name): void
    {
        $this->expectException(Exception::class);
        new UserName($name);
    }

    public function longNames(): array
    {
        return [
            ['UE69KHLM3Q8BzwM6ZDAYa'], // 21 letters
            ['UE69KHLM3Q8BzwM6ZDAYad'], // 22 letters
        ];
    }

    /**
     * @dataProvider longNames
     */
    public function test_long_name(string $name): void
    {
        $this->expectException(Exception::class);
        new UserName($name);
    }

    public function validNames(): array
    {
        return [
            ['ReiRev'],
            ['abc'], // 3 letters
            ['eFaNtc7hZMRXn8Ud7y2g'], // 20 letters
            ['れいれぶ']
        ];
    }

    /**
     * @dataProvider validNames
     */
    public function test_valid_name(string $name): void
    {
        $username = new UserName($name);
        $this->assertEquals($name, $username->toString());
        $this->assertEquals($name, $username->value());
        $this->assertEquals(['username' => $name], $username->toArray());
    }
}

```

いくつかピックアップして内容を見ていきましょう。
テストを実行する関数は、publicな関数にし、名前は`test_*`とするようにします。
基本的には`$this->assertEquals`を使用して、ある値が所望の値かどうかをチェックします(`test_valid_name`関数など)。
もし、エラーが生じるかどうかを確かめたい場合には、エラーが生じる箇所の前に`$this->expectException(Exception::class);`を書いておけば良いです。
`Exception`のインポートは忘れずにしておきましょう。

また、複数の入力パターンに対してテストを実行したい場合には、PHP Unit Testの[data provider](https://phpunit.de/manual/3.7/en/appendixes.annotations.html#appendixes.annotations.dataProvider)を使用することができます。
まず、複数の入力パターンを`shortNames`のように関数の形で定義し、それを入力として受けるテストの関数(ここでは`test_short_name`)に、`@dataProvider shortNames`のようなアノテーションをつけることで、複数の入力に対するテストを記述することができます。

`php artisan test`で、テストが全てパスすることを確認しましょう。

### ユーザー作成処理のテスト

[書籍リスト5.17](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)のユーザー作成処理のテストを作成してみましょう。

以下のコマンドで`UserTest.php`を作成します。

```bash
php artisan make:test UserTest
```

作成された`tests/Feature/UserTest.php`を以下のように編集します。
書籍に加えて、必要そうなテストを追加で作成しています。

```php:tests/Feature/UserTest.php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\ValueObjects\UserName;

use Exception;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create(): void
    {
        $username = new UserName('ReiRev');
        $user = User::create(['username' => $username]);

        $head = User::first();

        $this->assertEquals($user['username'], 'ReiRev');
        $this->assertEquals($head['username'], 'ReiRev');
    }

    public function test_duplicate_user_create(): void
    {
        $username = new UserName('ReiRev');
        $user = User::create(['username' => $username]);
        $this->expectException(Exception::class);
        $user = User::create(['username' => $username]);
    }
}


```

`use RefreshDatabase;`をクラス内に記述しておくことで、各テストごとに、すなわち各テストの関数の実行ごとに[データベースがリセットされます](https://laravel.com/docs/10.x/database-testing#resetting-the-database-after-each-test)。


以下でテストを実行し、問題なくテストが動作することを確認します。

```bash
php artisan make:test
```

## 参考文献

- [Config Laravel to run phpunit test with SQLite Database](https://5balloons.info/config-laravel-run-phpunit-test-sqlite-database/)
- [How do I test for multiple exceptions with PHPUnit?](https://stackoverflow.com/questions/1593834/how-do-i-test-for-multiple-exceptions-with-phpunit)
