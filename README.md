# pukiwiki-region-wikiwikilike

## 概要
[region.inc.php](https://pukiwiki.osdn.jp/?%E8%87%AA%E4%BD%9C%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3/region.inc.php)をWikiWiki風の見た目にカスタマイズしたものです。  
プラグイン名をregion.inc.phpと同一にしているため、region.inc.phpと同居できません。  
ついでにtableタグをやめてdivタグにしています。

## インストール
`region.inc.php`と`endregion.inc.php`をpluginディレクトリに置いてください。

## 使い方
region.inc.phpと同じです`opened`,`closed`も対応してます。
```
#region(タイトル)
本文
#endregion
```
wikiwiki使用者が混乱しないよう、独自オプションを付ける予定はありません。(今の所)

## license
GPL
