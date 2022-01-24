# PukiWiki用プラグイン<br>JSON-LD出力 jsonld.inc.php

JSON-LD を出力する[PukiWiki](https://pukiwiki.osdn.jp/)用プラグイン。  
ページの情報に基づくJSON-LD構造化データを生成し出力します。  
具体的には、記事情報 Article とパンくずリスト情報B readcrumbList を生成します。  
ウィキの構造を検索エンジンにより良く伝えるため（SEO）に役立ちます。

|対象PukiWikiバージョン|対象PHPバージョン|
|:---:|:---:|
|PukiWiki 1.5.3 ~ 1.5.4RC (UTF-8)|PHP 7.4 ~ 8.1|

## インストール

jsonld.inc.php を PukiWiki の plugin ディレクトリに配置してください。

## 使い方

```
#jsonld
```

MenuBarなど全画面共通で表示されるページに挿入してください。  
もしくは、次のコードをスキンファイルHTML内の</body>閉じタグ直前に挿入してください。

```
<?php if (exist_plugin_convert('jsonld')) echo do_plugin_convert('jsonld'); ?>
```

なお、本プラグインを挿入できるのは1ページにつき1箇所のみです。

## 設定

ソース内の下記の定数で動作を制御することができます。

|定数名|値|既定値|意味|
|:---|:---:|:---:|:---|
|PLUGIN_JSONLD_ARTICLE| 0 or 1| 1|1：Article （記事情報）を出力, 0：無効|
|PLUGIN_JSONLD_BREADCRUMBLIST| 0 or 1| 1|1：BreadcrumbList （パンくずリスト情報）を出力, 0：無効|

