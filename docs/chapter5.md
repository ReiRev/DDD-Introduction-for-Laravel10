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

### ユーザー作成処理のテスト

[書籍リスト5.17](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)のユーザー作成処理のテストを作成してみましょう。

まず、以下のコマンドで`UserTest.php`を作成します。

```bash
php artisan make:test UserTest
```

作成された`tests/Feature/UserTest.php`を以下のように編集します。

```php:tests/Feature/UserTest.php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Services\UserService;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_create(): void
    {
        $reirev = new User(['name' => 'Rei Rev']);
        $reirev->save();

        $head = User::find(1)->get()->first();

        $this->assertEquals($head['name'], 'Rei Rev');
    }
}
```

`use RefreshDatabase;`をクラス内に記述しておくことで、テストごとに[データベースがリセットされます](https://laravel.com/docs/10.x/database-testing#resetting-the-database-after-each-test)。

以下でテストを実行し、問題なくテストが動作することを確認します。

```bash
php artisan make:test UserTest
```

## 参考文献

- [Config Laravel to run phpunit test with SQLite Database](https://5balloons.info/config-laravel-run-phpunit-test-sqlite-database/)
