# DDD-Introduction-for-Laravel10

本プロジェクトでは、成瀬允宣氏著の「[ドメイン駆動設計入門](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)」のLaravel 10を用いた実装例を示します。

当該書籍では、ドメイン駆動設計(Domain Driven Deplopment, DDD)を用いたWEBアプリケーションの開発例が示されており、素晴らしい著作であることは言うまでもありません。
しかしながら実装はC#で示されているため
、例えばPHPフレームワークの代表例であるLaravelなどでDDDによる実装を行いたい場合には、具体的な実装例が分かりづらくなってしまいます。

そこで本プロジェクトでは、「[ドメイン駆動設計入門](https://www.amazon.co.jp/%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80-%E3%83%9C%E3%83%88%E3%83%A0%E3%82%A2%E3%83%83%E3%83%97%E3%81%A7%E3%82%8F%E3%81%8B%E3%82%8B%EF%BC%81%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AE%E5%9F%BA%E6%9C%AC-%E6%88%90%E7%80%AC-%E5%85%81%E5%AE%A3-ebook/dp/B082WXZVPC?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&crid=2SPIX3DU2EUW2&keywords=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E5%85%A5%E9%96%80&qid=1686451938&sprefix=%E3%83%89%E3%83%A1%E3%82%A4%E3%83%B3%E9%A7%86%E5%8B%95%E8%A8%AD%E8%A8%88%E3%81%AB%E3%82%85%E3%81%86%E3%82%82n%2Caps%2C700&sr=8-1&linkCode=ll1&tag=reirev0e-22&linkId=921753cd089b48613204b35f1d241358&language=ja_JP&ref_=as_li_ss_tl)」のLaravelによる実装例を示すことで、Laravel開発者のDDD実装の手助けとなることを目指します。

これが皆様の開発の手助けとなれば幸いです。ご意見等ございましたら、コメント欄やIssueでお伝えいただければと思います。

## 前提条件

本プロジェクトでは、Docker上で作業を行います(Dockerの知識は必要ありません)。
Docker上で作業する際、Visual Studio Codeの[Dev Containers 拡張機能](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)を使用すると開発が便利になりますので、
Visual Studio Codeと拡張機能のインストールを推奨します。

また、PHPとPHPのパッケージマネージャーである[Composer](https://getcomposer.org/)をローカルマシンにインストールしておく必要があります。

## chapter 0: Laravel及びDocker環境のセットアップ

以下でlaravel v10.0のプロジェクトをsrcフォルダ内に作成。

```[bash]
composer create-project laravel/laravel src 10.0 --prefer-dist
```

srcフォルダに移動し、sailをインストールする。
これにより、Docker上での作業を行うとができる。
また、`php artisan sail:install --devcontainer`で`--devcontainer`オプションをつけることにより、
Visual Studio Codeの[Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)の設定フォルダが`src/.devcontainer`に作成される。
これにより、開発がかなり便利になる。

```[bash]
cd src
composer require laravel/sail --dev
php artisan sail:install --devcontainer
```

rootディレクトリに戻り、srcディレクトリをVisual Studio Codeで開き直す。

```[bash]
cd ..
code src
```

Visual Studio CodeのCommand Paletteを開き、`Dev Containers: Reopen in Container`をクリックすると、Docker上でコードの編集を行うことができます。

以下でサーバーを立ち上げます。

```[bash]
php artisan serve
```

表示されたlocalhostのURLをブラウザで立ち上げ、Laravelのページが表示されていれば、セットアップ完了です。

```[bash]
INFO  Server running on [http://127.0.0.1:8000].  
```
