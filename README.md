# TSUTAYAアプリ　リニューアルAPI


## API Document

本システムのAPIドキュメントはインタラクティブドキュメント OpenAPI仕様（OAS）のSwaggerを使用しています。  
下記にアクセスして、使用を確認できます。  
[TSUTAYA APP Renew Swagger Document](https://dev.api.tsutaya-passport.tsite.jp/tapp/api/v1/docs/index.html)  

## Development environment Install
### 概要
開発を行う為の、セットアップ手順になります。<br>
本リポジトリからdevelopブランチをCloneしてください。
プロジェクトルート下でcomposert installを行い、ライブラリのインストールを行って下さい。


## Unit Test
### 概要
TOP関連のインポートと、在庫関連各APIへの500エラーが出ないことアクセスチェックを行います。<br>
下記を実行して、エラーがないことを確認してください。

#### 通常実行
```./vendor/bin/phpunit```

#### テストケース別にログを出力
```./vendor/bin/phpunit --testdox```

#### 対象のテストクラスのみをテスト
./vendor/bin/phpunit ./tests/AccessTest

※テストケースについては実装完了後に追加<br>
