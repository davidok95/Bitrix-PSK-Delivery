<?
namespace Davidok95\Psk;

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\Validator;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class PskRatesInEuroTable extends DataManager
{
    public static function getTableName()
    {
        return 'davidok95_psk_ratesineuro';
    }

    public static function getMap()
    {
        return array(
            new IntegerField("ID", array(
                "autocomplete" => true,
                "primary" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_ID"),
            )),
            new StringField("WEIGHT", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_WEIGHT"),
            )),
			new StringField("ZONE", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_ZONE"),
            )),
			new StringField("PRICE", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_PRICE"),
            )),
        );
    }
}
