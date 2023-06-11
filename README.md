# DDD-Introduction-for-laravel10

成瀬允宣 氏著の「ドメイン駆動設計入門」のLaravel 10を用いた実装例

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
