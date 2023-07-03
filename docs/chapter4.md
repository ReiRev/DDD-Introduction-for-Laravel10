# Chapter 4: 不自然さを解決する「ドメインサービス」

## Docker環境で開発

あらかじめ、`src`フォルダをVisual Studio Codeで開き、Command Paletteの`Dev Containers: Reopen in Container`でDocker環境に入っておきます。

## ドメインサービスとは

[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)では、ドメインサービスについて以下のように記述されています。

> システムには値オブジェクトやエンティティに記述すると不自然になってしまう振る舞いが存在します。
> ドメインサービスはそういった不自然さを解決するオブジェクトです。

具体的には、User用のドメインサービスとして、Userオブジェクトがすでに確認するかのメソッドを定義しています。
その他にも、[書籍 Chapter4.5](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)では、物流サービスの「輸送」の例を挙げています。
確かに「輸送」は倉庫や届け物が担う役割ではないため、新たにドメインサービスを作成するのが適していそうです。

## Laravelでのドメインサービス

[書籍](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)では、ドメインサービスの一つとして、同じユーザー名が存在するかどうかの確認をするexistsメソッドをUserServiceに実装しています。
Eloquentでは、もともとexistsメソッドが実装されており、以下のようなコードで同じユーザー名が存在するかどうかを確認できますので、一見したところ、UserServiceにexistsメソッドを実装しなくて良いように思えます。

```php
User::where("username", $username)->exists()
```

しかしながら、この存在確認処理はさまざまなところで使われることが想定されます。
そして、ユーザーの存在確認処理が変更される可能性は十分にあり得ます。
例えば、ユーザー名だけではなく、メールアドレスもチェックして存在確認を行いたくなるかもしれません。
その際、存在確認処理を全て書き直すことは非常に面倒です。
そのため、UserServiceクラスにexistsメソッドを実装することは十分価値があるでしょう。

`app/DomainServices`というフォルダを作り、以下のように実装します。
Laravelでは、Serviceという言葉がたくさん出てくるため、ここでは区別するために`DomainServices`というフォルダを作っています。

```php:app/DomainServices/UserService.php
<?php

namespace App\DomainServices;

use App\Models\User;

class UserService
{
    public function exists(User $user)
    {
        return User::where("username", $username)->exists();
    }
}

```

