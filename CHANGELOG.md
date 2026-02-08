# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2] - 2026-02-08

### Fixed
- URLからの投稿タイプ検出を追加し、CPT間の誤マッチを防止
- キーワード検索の最低一致率ゲート（50%以上）を追加し、単一キーワードの誤検出を防止
- 最低スコア閾値を40から45に引き上げ
- キーワード最低文字数を2文字から3文字に引き上げ（mb_strlen対応）

## [1.0.1] - 2025-12-18

### Added
- 投稿タイプ選択に「すべての投稿タイプを対象にする」オプションを追加
- 移動先ページ検出機能を強化（類似スラッグ検索、タイトルキーワード検索、スコアリング）

### Changed
- 個別の投稿タイプ選択をアコーディオン形式に変更（「すべて」がOFFの場合のみ表示）

## [1.0.0] - 2025-12-18

### Added
- 初回リリース
- カスタム404エラーページの表示
- リダイレクト候補の提示
- 関連性の高い記事の表示
- 最新記事の表示
- 配色テーマ選択機能
- 構造化マークアップ対応
- フッター機能
- リダイレクト動作のカスタマイズ

[1.0.2]: https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-custom-404/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-custom-404/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-custom-404/releases/tag/v1.0.0
