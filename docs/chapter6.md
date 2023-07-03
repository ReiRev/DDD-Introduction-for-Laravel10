# Chapter 6: ユースケースを実現する「アプリケーションサービス」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## アプリケーションサービスとは

ここまで、値オブジェクトとEloquentモデル(エンティティ)の作成を行ってきました。
アプリケーションサービスは、[ドメイン駆動設計入門](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)曰く"ユースケースを実現するオブジェクト"です。
ここでは書籍にならい、ユーザーのCRUD(Create, Read, Update, Delete)の実装をLaravelで行っていきましょう。

## フォルダの作成

アプリケーションサービスは`app/ApplicationServices`内に作成しましょう。
また、今回はUserのCRUDを作成していきますので、さらに`app/ApplicationServices/Users`フォルダを作成します。
この中に、`UserStoreService`, `UserUpdateService`, `UserShowService`, `UserDestroyService`を作成していきます。
Laravel流に、書籍のクラス名は、Create->Store, Register->Create, GetInfo->Show, Delete->Destroyに変更しています。
また、書籍6.4節で書かれている通り、コードの凝集度を考慮してサービスごとに個別のクラスを作成します。

## DTOの作成

アプリケーションサービスの入出力(入力引数、返り値)には、Eloquentのモデルを使用しません。
なぜなら、Eloquentのモデルをアプリケーションサービスより上位のレイヤーで使用できるようにしてしまうと、予期せぬ動作を引き起こしてしまう可能性があるからです。
例えば、Elqouentの`save()`メソッドを呼び出してしまうことで勝手にデータベースにデータを保存したり、あるいは保存したつもりでも`save()`メソッドの呼び出しを忘れて保存できていなかったりということがあると困るわけです。

そこでアプリケーションサービスの入出力には、Data Transfer Object(DTO)を使用します。
DTOにはEloquentのモデルのプロパティのうち、必要なもののみを保持しておき、`save()`などのメソッドは実装しません。
これにより、ドメインオブジェクトとアプリケーションサービスをうまく分離し、危険な操作を防ぐことができます。

DTOの作成には、[Laravel-data](https://spatie.be/docs/laravel-data/v3/introduction)を使用していきましょう。

まずはcomposerを使ってLaravel-dataをインストールします。

```bash
composer require spatie/laravel-data
```

次に、以下で`app/DataTransferObjects/UserData.php`を作成します。

```bash
php artisan make:data --namespace=DataTransferObjects UserData
```

雛形が作成されていますので、以下のように作成しましょう。
ここではusernameのみを上位層に受け渡し、idやcreated_at, updated_atは秘匿しておきます。

```php:app/DataTransferObjects/UserData.php
<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $username
    ) {
    }
}

```

具体的な使用方法は、以下のアプリケーションサービスの作成とともに見ていきます。

## UserStoreServiceの実装

UserStoreServiceをいきなり始める前に、まずはUserStoreServiceのインターフェースを作成します。
というのも、例えば実際の開発においては、UserStoreServiceの実装が完了しておらず、Mockなどを作成して開発を進める場合があるからです。
Mockなどの動作方法については、Chapter7で扱います。

```php:app/ApplicationServices/Users/UserStoreServiceInterface.php
<?php

namespace App\ApplicationServiecs\Users;

use App\DataTransferObjects\UserData;

interface UserStoreServiceInterface
{
    public function store(Userdata $userdata): void;
}

```
