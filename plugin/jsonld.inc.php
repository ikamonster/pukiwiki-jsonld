<?php
/*
PukiWiki - Yet another WikiWikiWeb clone.
jsonld.inc.php, v1.02 2020 M.Taniguchi
License: GPL v3 or (at your option) any later version

JSON-LDを出力するプラグイン。

ページの情報に基づくJSON-LD構造化データを生成し出力します。
具体的には、記事情報 Article とパンくずリスト情報 BreadcrumbList を生成します。
ウィキの構造を検索エンジンにより良く伝えるため（SEO）に役立ちます。

【使い方】
#jsonld

本プラグインは、MenuBar など全画面共通で表示されるページに挿入してください。
もしくは、次のコードをスキンファイル（skin/pukiwiki.skin.php等）HTML内の</body>閉じタグ直前に挿入してください。
 <?php if (exist_plugin_convert('jsonld')) echo do_plugin_convert('jsonld'); ?>
なお、本プラグインを挿入できるのは1ページにつき1箇所のみです。
*/

// 出力したいJSON-LD情報を選び、値を 1 にしてください。
if (!defined('PLUGIN_JSONLD_ARTICLE'))        define('PLUGIN_JSONLD_ARTICLE',        1); // 1：Article （記事情報）を出力, 0：無効
if (!defined('PLUGIN_JSONLD_BREADCRUMBLIST')) define('PLUGIN_JSONLD_BREADCRUMBLIST', 1); // 1：BreadcrumbList （パンくずリスト情報）を出力, 0：無効

function plugin_jsonld_convert() {
	if (!PLUGIN_JSONLD_ARTICLE && !PLUGIN_JSONLD_BREADCRUMBLIST) return '';
	// if (!PKWK_ALLOW_JAVASCRIPT) return '';	// JavaScriptではなくJSONなので無視

	global	$modifier, $defaultpage, $page_title, $title;

	// 二重起動禁止
	static	$included = false;
	if ($included) return '';
	$included = true;

	$script = get_script_uri();
	$isHome = ($title == $defaultpage);
	$long_title = (!$isHome ? $title . ' | ' : '') . $page_title;
	$thisPageUri = $script . (!$isHome ? '?' . str_replace('%2F', '/', urlencode($title)) : '');
	$modifiedDate = date('Y-m-d\TH:i:sP', get_filetime($title));
	$jsonOption = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

	// Article（記事情報）生成
	if (PLUGIN_JSONLD_ARTICLE) {
		$article = array(
			'@context' => 'http:'.'//schema.org',
			'@type' => 'Article',
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id' => $thisPageUri
			),
			'datePublished' => $modifiedDate,
			'dateModified' => $modifiedDate,
			'author' => array(
				'@type' => 'Person',
				'name' => $modifier
			),
			'publisher' => array(
				'@type' => 'Organization',
				'name' => $page_title
			),
			'headline' => $long_title
		);
		$article = '<script type="application/ld+json">' . json_encode($article, $jsonOption) . '</script>';
	} else $article = '';

	// BreadcrumbList（パンくずリスト）生成
	if (PLUGIN_JSONLD_BREADCRUMBLIST && !$isHome ) {
		$names = explode('/', $title);
		$path = '';
		$i = 0;

		$bread = array();
		$bread[] = array(
			'@type' => 'ListItem',
			'position' => ++$i,
			'name' => $defaultpage,
			'item' => $script
		);

		foreach ($names as $name) {
			$path .= (($path != '')? '/' : '') . urlencode($name);
			$bread[] = array(
				'@type' => 'ListItem',
				'position' => ++$i,
				'name' => $name,
				'item' => $script . '?' . $path
			);
		}

		$bread = array(
			'@context' => 'http:'.'//schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $bread
		);
		$bread = '<script type="application/ld+json">' . json_encode($bread, $jsonOption) . '</script>';
	} else $bread = '';

	return $article . $bread;
}
