<?
namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;

class PskHandler extends Base
{
	public static function getClassTitle()
	{
		return 'Доставка PSK';
	}

	public static function getClassDescription()
	{
		return 'Доставка, стоимость которой зависит только от веса и местоволожения';
	}

	protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
	{
		$result = new CalculationResult();
		$price = floatval($this->config["MAIN"]["PRICE"]);
		$weight = floatval($shipment->getWeight()) / 1000;

		$result->setDeliveryPrice(roundEx($price * $weight, 2));
		$result->setPeriodDescription('1 день');

		return $result;
	}

	protected function getConfigStructure()
	{
		return array(
			"MAIN" => array(
				"TITLE" => 'Настройка обработчика',
				"DESCRIPTION" => 'Настройка обработчика',
				"ITEMS" => array(
					/* "PRICE" => array(
						"TYPE" => "NUMBER",
						"MIN" => 0,
						"NAME" => 'Стоимость доставки за грамм'
					) */
				)
			)
		);
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
