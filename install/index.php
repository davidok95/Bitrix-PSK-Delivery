<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

\Bitrix\Main\Loader::includeModule('iblock');
Loc::loadMessages(__FILE__);

class davidok95_psk extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        
        $this->MODULE_ID = 'davidok95.psk';
        $this->MODULE_NAME = Loc::getMessage('DAVIDOK95_PSK_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DAVIDOK95_PSK_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('DAVIDOK95_PSK_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'http://davidok95.ru';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
    }

    public function doUninstall()
    {
		$this->uninstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
		global $DB;

		$this->installIblockType();
		$this->installRatesInEuroTable();
	}

    public function uninstallDB()
    {
		global $DB;

		$DB->StartTransaction();
		if( ! CIBlockType::Delete("davidok95_psk"))
		{
			$DB->Rollback();
			echo "Delete error !";
		}
		$DB->Commit();
    }

	public function installRatesInEuroTable()
	{
	}

	public function installIblockType()
	{
		global $DB;
		$arFields = array(
			"ID" => "davidok95_psk",
			"SECTIONS" => "N",
			"IN_RSS" => "N",
			"SORT" => 100,
			"LANG" => array(
				"en" => array(
					"NAME" => "Delivery PSK",
					"SECTION_NAME" => "",
					"ELEMENT_NAME" => "Элемент"
				),
				"ru" => array(
					"NAME" => "Доставка PSK",
					"SECTION_NAME" => "",
					"ELEMENT_NAME" => "Element"
				)
			)
		);
		$obBlocktype = new CIBlockType;
		$DB->StartTransaction();
		$res = $obBlocktype->Add($arFields);
		if( ! $res)
		{
		   $DB->Rollback();
		   echo "Error: " . $obBlocktype->LAST_ERROR . "<br>";
		   die();
		}
		else
			$DB->Commit();
	}
}
