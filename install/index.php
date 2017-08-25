<?php
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Davidok95\Psk\PskRatesInEuroTable;
use Davidok95\Psk\PskDestinationsTable;

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
		if (Loader::includeModule($this->MODULE_ID))
        {
            PskRatesInEuroTable::getEntity()->createDbTable();
            PskDestinationsTable::getEntity()->createDbTable();
        }
	}

    public function uninstallDB()
    {
		global $DB;

		if (Loader::includeModule($this->MODULE_ID))
        {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(PskRatesInEuroTable::getTableName());
            $connection->dropTable(PskDestinationsTable::getTableName());
        }
	}
}