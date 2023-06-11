# Chapter 0: Laravel及びDocker環境のセットアップ

まずは、LaravelとDockerの開発環境を構築していきましょう。

以下でlaravel v10.0のプロジェクトをsrcフォルダ内に作成します。

```[bash]
composer create-project laravel/laravel src 10.0 --prefer-dist
```

srcフォルダに移動し、sailをインストールします。
これにより、Docker上での作業を行うとができます。
また、`php artisan sail:install --devcontainer`で`--devcontainer`オプションをつけることにより、
Visual Studio Codeの[Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)の設定フォルダが`src/.devcontainer`に作成されます。
これにより、開発がかなり便利になります。

```[bash]
cd src
composer require laravel/sail --dev
php artisan sail:install --devcontainer
```

rootディレクトリに戻り、srcディレクトリをVisual Studio Codeで開き直します。

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
