# Kashiwazaki SEO Custom 404

![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)
![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)
![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)

HTTP 404エラー時にオリジナルのエラーページを表示し、リダイレクト候補や関連性の高い記事・最新記事を提示するWordPressプラグインです。

## 機能

- カスタム404エラーページの表示
- リダイレクト候補の提示
- 関連性の高い記事の表示
- 最新記事の表示
- 配色テーマ選択機能
- 構造化マークアップ対応
- フッター機能
- リダイレクト動作のカスタマイズ

## インストール

1. このプラグインをダウンロード
2. WordPressの`wp-content/plugins/`ディレクトリにアップロード
3. 管理画面の「プラグイン」メニューからプラグインを有効化
4. 「設定」→「Kashiwazaki SEO Custom 404」で設定を調整

## 要件

- WordPress 5.0以上
- PHP 7.0以上

### 技術的詳細
- **PHP要件**: 短配列構文 `[]` を使用しているため、技術的にはPHP 5.4以上で動作可能ですが、セキュリティサポートのため **PHP 7.0以上** を推奨します
- **WordPress要件**: Settings API (`register_setting`)、`add_menu_page`、`wp_parse_args` などを使用しており、技術的にはWordPress 3.0以上で動作可能ですが、現代的なサポートとセキュリティのため **WordPress 5.0以上** を推奨します

## ライセンス

GPL-2.0 or later

## 著者

柏崎 剛 (Tsuyoshi Kashiwazaki)
- Website: https://www.tsuyoshikashiwazaki.jp
