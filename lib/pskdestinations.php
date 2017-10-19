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

class PskDestinationsTable extends DataManager
{
    public static function getTableName()
    {
        return 'davidok95_psk_destinations';
    }

    public static function getMap()
    {
        return array(
            new IntegerField("ID", array(
                "autocomplete" => true,
                "primary" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_ID"),
            )),
            new StringField("CITY", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_CITY"),
            )),
			new StringField("REGION", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_REGION"),
            )),
			new StringField("CITY_RUS", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_CITY_RUS"),
            )),
			new StringField("ZONE", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_ZONE"),
            )),
			new StringField("ZIP_CODE", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_ZIP_CODE"),
            )),
			new StringField("PERIOD", array(
                "required" => true,
                "title" => Loc::getMessage("DAVIDOK95_PSK_PERIOD"),
            )),
        );
    }
}
