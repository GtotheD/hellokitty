# TSUTAYAアプリ リニューアルAPI

## 1．APIについて
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

## Coding Policy
### 概要
コーディングの時の方針です。<br>

#### クラス実装

##### クラスの役割を認識する
クラスの役割を認識し統一化しないと、ロジックが分散し、どこで何を行うかがわかりずらく、ソース解読が困難になるのを避ける。

##### メソッド独自の引数は極力なくす。
メソッドの引数は、極力メソッド独自の引数としない。
同じクラス内のメソッド似たようなパラメーターなのに違う指定を行う可能性が高く統一されない。<br>
よって、Setter,Getterを使い、メンバ変数を使うことで、メソッドの使いかたを統一化する。


## 2．運用ルール

※ブランチとチケットはセットで作成するように！

## ブランチについて
### 構成
`release_v3` (商用リリースブランチ) <br>
　∟ `develop_v3` (←※ここから作業ブランチ切る) <br>
 　　∟ `featur/XXXXXX_XXXXX_XXXXXX`

### ブランチの命名

```
feature/{edit || bugfix}_{チケット番号}_{短いタイトル}
```

・feature/edit_：新規・追加実装 <br>
・feature/bugfix_：バグの改修 <br>


```
例)【TSUTAYAアプリ】商品一覧情報取得APIの除外条件解除対応 <br>
チケット：https://redmine.tsutaya.co.jp/redmine/issues/104199
⇒ feature/edit_104199_products_api
```

## プルリクエスト(PR)について
①PRとチケットのタイトルは合わせる。 <br>
②PRのメモには「チケットのURL」と、「内容」を記載 <br>
③コミットは、ある程度こまめに <br>
④コミット時には、コメントを付ける

```
例）【TSUTAYAアプリ】商品一覧情報取得APIの除外条件解除対応 <br>
チケット：https://redmine.tsutaya.co.jp/redmine/issues/104199 <br>
PR：http://10.80.2.11/tapdev/tap_r_api/pull/627
```

