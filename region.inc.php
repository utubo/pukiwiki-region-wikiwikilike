<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: region_wikiwikilike.inc.php,v 1.0 2019/09/26 15:50:00 xxxxx Exp $
//
// regionをWikiWiki風の見た目にカスタマイズしたものです。
// プラグイン名をregionと同一にしているため、regionと同居できません。
// ついでに
// ・tableタグをやめてdivタグにしています。
// ・展開後のブラケット(左に表示されるコの字)は削除しました。
// ・変数名description→descriptionに統一しました。
// ライセンス
// GPL

define('REGION_CONTAINER_STYLE', '
	position:relative;
	padding-left:20px;
');
define('REGION_BUTTON_STYLE', '
	border:1px solid;
	cursor:pointer;
	height:14px;
	left:0;
	line-height:14px;
	position:absolute;
	text-align:center;
	top:0;
	width:14px;
');


function plugin_region_convert()
{
	static $builder = 0;
	if ($builder==0) $builder = new RegionWPluginHTMLBuilder();

	// static で宣言してしまったので２回目呼ばれたとき、前の情報が残っていて変な動作になるので初期化。
	$builder->setDefaultSettings();

	// 引数が指定されているようなので解析
	if (func_num_args() >= 1) {
		$args = func_get_args();
		$builder->setDescription(array_shift($args));
		foreach($args as $value) {
			// opened が指定されたら初期表示は開いた状態に設定
			if (preg_match("/^open/i", $value)) {
				$builder->setOpened();
			// closed が指定されたら初期表示は閉じた状態に設定。
			} elseif (preg_match("/^close/i", $value)) {
				$builder->setClosed();
			}
		}
	}
	// HTML返却
	return $builder->build();
}


// クラスの作り方⇒http://php.s3.to/man/language.oop.object-comparison-php4.html
class RegionWPluginHTMLBuilder
{
	var $description;
	var $isopened;
	var $scriptVarName;
	//↓ buildメソッドを呼んだ回数をカウントする。
	//↓ これは、このプラグインが生成するJavaScript内でユニークな変数名（被らない変数名）を生成するために使います
	var $callcount;

	function RegionPluginHTMLBuilder() {
		$this->callcount = 0;
		$this->setDefaultSettings();
	}
	function setDefaultSettings() {
		$this->description = "...";
		$this->isopened = false;
	}
	function setClosed() { $this->isopened = false; }
	function setOpened() { $this->isopened = true; }
	// convert_html()を使って、概要の部分にブランケットネームを使えるように改良。
	function setDescription($description) {
		$this->description = convert_html($description);
	}
	function build() {
		$this->callcount++;
		$html = array();
		// 以降、HTML作成処理
		if ($this->callcount == 1) {
			// scriptタグは1個だけ出力すればOK
			array_push($html, $this->buildJavaScript());
		}
		array_push($html, $this->buildContainerHeaderHtml());
		array_push($html, $this->buildButtonHtml());
		array_push($html, $this->buildDescriptionHtml());
		array_push($html, $this->buildContentHtml());
		return join($html);
	}

	// 折りたたみのscriptタグ
	function buildJavaScript() {
		return <<<EOD
<script>
function tglRgn(id) {
	var b = document.getElementById('rgn_description'+id).style.display != 'none';
	document.getElementById('rgn_description'+id).style.display = b ? 'none' : 'block';
	document.getElementById('rgn_content'+id).style.display = b ? 'block' : 'none';
	document.getElementById('rgn_button'+id).textContent = b ? '-' : '+';
}
</script>
EOD;
	}

	// 折りたたみ全体のdivヘッダ部分。ここの<div>の閉じタグは endregion 側にある。
	function buildContainerHeaderHtml() {
		$constant = 'constant';
		return <<<EOD
<div class="rgn-container" style="{$constant('REGION_CONTAINER_STYLE')}">
EOD;
	}

	// 展開ボタンの部分。
	function buildButtonHtml() {
		$constant = 'constant';
		$button = ($this->isopened) ? "-" : "+";
		return <<<EOD
<div id="rgn_button$this->callcount" class="rgn-button" onclick="tglRgn($this->callcount)" style="{$constant('REGION_BUTTON_STYLE')}">$button</div>
EOD;
	}

	// 縮小表示しているときの表示内容。
	function buildDescriptionHtml() {
		$descriptionstyle = ($this->isopened) ? "display:none;" : "display:block;";
		return <<<EOD
<div id="rgn_description$this->callcount" style="$descriptionstyle" class="rgn-description">$this->description</div>
EOD;
	}

	// 展開表示しているときの表示内容ヘッダ部分。ここの<div>の閉じタグは endregion 側にある。
	function buildContentHtml() {
		$contentstyle = ($this->isopened) ? "display:block;" : "display:none;";
		return <<<EOD
<div id="rgn_content$this->callcount" style="$contentstyle" class="rgn-content">
EOD;
	}

}// end class RegionPluginHTMLBuilder

?>
