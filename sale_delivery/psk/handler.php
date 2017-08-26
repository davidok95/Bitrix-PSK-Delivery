<?
namespace Sale\Handlers\Delivery;

\Bitrix\Main\Loader::includeModule("davidok95.psk");

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;
use Davidok95\Psk\PskRatesInEuroTable;
use Davidok95\Psk\PskDestinationsTable;

class PskHandler extends Base
{
	public static function getClassTitle()
	{
		return "Доставка PSK";
	}

	public static function getClassDescription()
	{
		return "Доставка, стоимость которой зависит только от веса и местоволожения";
	}

	protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
	{
		$result = new CalculationResult();
		$price = floatval($this->config["MAIN"]["PRICE"]);
		$weight = ceil(floatval($shipment->getWeight()) / 1000);


		// get city name
		$order = $shipment->getCollection()->getOrder(); // заказ
		$props = $order->getPropertyCollection(); 
		$locationCode = $props->getDeliveryLocation()->getValue(); 
		$rsLoc = \Bitrix\Sale\Location\LocationTable::getByCode($locationCode);
		$arLoc = $rsLoc->Fetch();
		$rsLocName = \Bitrix\Sale\Location\Name\LocationTable::getList(array(
			"filter" => array("LOCATION_ID" => $arLoc["ID"], "LANGUAGE_ID" => "en"),
		));
		if ($arLocName = $rsLocName->fetch())
			$cityName = $arLocName["NAME"];

		// get zone
		$zone = false;
		$rsDest = PskDestinationsTable::getList(array(
			"filter" => array("CITY" => $cityName),
		));
		if ($arDest = $rsDest->Fetch())
		{
			$zone = $arDest["ZONE"];
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', "CityName:" . print_r($cityName,true) . "\n", FILE_APPEND);
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', "Zone:" . print_r($zone,true) . "\n", FILE_APPEND);
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', "Weight:" . print_r($weight,true) . "\n", FILE_APPEND);

		// get price euro
		$priceEuro = false;
		if ($zone !== false)
		{
			$rsRate = PskRatesInEuroTable::getList(array(
				"filter" => array(
					"WEIGHT" => $weight,
					"ZONE" => $zone,
				),
			));
			if ($arRate = $rsRate->Fetch())
			{
				$priceEuro = $arRate["PRICE"];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		// process currency
		if (\Bitrix\Main\Loader::includeModule("currency"))
		{
			$resCurrency = \Bitrix\Currency\CurrencyTable::getList(array(
				"filter" => array("CURRENCY" => "EUR")
			));
			if ($currency = $resCurrency->fetch())
			{
				$priceRub = $priceEuro * $currency["CURRENT_BASE_RATE"];
			}
		}
		else
			$priceRub = $priceEuro;

		$result->setDeliveryPrice(roundEx($priceRub, 2));
		$result->setPeriodDescription("", true);

		return $result;
	}

	protected function getConfigStructure()
	{
		return array(
			"MAIN" => array(
				"TITLE" => "Настройка обработчика",
				"DESCRIPTION" => "Настройка обработчика",
				"ITEMS" => array(
					"PRICE" => array(
						"TYPE" => "NUMBER",
						"MIN" => 0,
						"NAME" => "Стоимость доставки за грамм"
					)
				)
			)
		);
	}

	public function isCompatible(\Bitrix\Sale\Shipment $shipment)
	{
		$calcResult = self::calculateConcrete($shipment);

		if ($calcResult == false)
			return false;

		return $calcResult->isSuccess();
	}

	public function isCalculatePriceImmediately()
	{
		return true;
	}

	public static function whetherAdminExtraServicesShow()
	{
		return true;
	}
}
?>
