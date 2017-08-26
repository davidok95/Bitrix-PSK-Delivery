<?
if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

IncludeModuleLangFile(__FILE__);

$aMenu[] = array(
	"parent_menu" => "global_menu_store",
	"sort" => 700,
	"text" => "Служба доставки PSK",
	"title" => "Служба доставки PSK",
	"url" => false,
	"icon" => "sale_menu_cdek",
	"page_icon" => "util_page_icon",
	"items_id" => "menu_util_russianpostcalc",
	"items" => array (
		array(
			"text" => "Ставки в евро",
			"title" => "Ставки в евро",
			"url"  => "davidok95_psk_edit_ratesineuro.php",
			"icon" => false,
			"page_icon" => "form_page_icon",
		),
		array(
			"text" => "Местоположения",
			"title" => "Местоположения",
			"url"  => "davidok95_psk_edit_destinations.php",
			"icon" => false,
			"page_icon" => "form_page_icon",
		),
	),
);
return $aMenu;
?>
