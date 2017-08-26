<?
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/include.php"); // инициализация модуля
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/prolog.php"); // пролог модуля


\Bitrix\Main\Loader::includeModule('davidok95.psk');

use Bitrix\Main\Localization\Loc;
use \Davidok95\Psk\PskRatesInEuroTable;

// подключим языковой файл
Loc::loadMessages(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("subscribe");
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
?>


<?
// здесь будет вся серверная обработка и подготовка данных
$sTableID = "davidok95_psk_ratesineuro"; // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка


// проверку значений фильтра для удобства вынесем в отдельную функцию
function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;
    
    /* 
       здесь проверяем значения переменных $find_имя и, в случае возникновения ошибки, 
       вызываем $lAdmin->AddFilterError("текст_ошибки"). 
    */
    
    return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
}

// опишем элементы фильтра
$FilterArr = Array(
    "find_id",
    "find_weight",
    "find_zone",
    "find_price",
);

// инициализируем фильтр
$lAdmin->InitFilter($FilterArr);

// если все значения фильтра корректны, обработаем его
if (CheckFilter())
{
    // создадим массив фильтрации для выборки CRubric::GetList() на основе значений фильтра
	$arFilter = array();
	if ($find_id)
		$arFilter["ID"] = $find_id;
	if ($find_weight)
		$arFilter["WEIGHT"] = $find_weight;
	if ($find_zone)
		$arFilter["ZONE"] = $find_zone;
	if ($find_price)
		$arFilter["PRICE"] = $find_price;
}

// сохранение отредактированных элементов
if($lAdmin->EditAction() && $POST_RIGHT=="W")
{
    // пройдем по списку переданных элементов
    foreach($FIELDS as $ID => $arFields)
    {
        if( ! $lAdmin->IsUpdated($ID))
            continue;
        
        // сохраним изменения каждого элемента
        $ID = IntVal($ID);
		$rsData = PskRatesInEuroTable::getList(array(
			'filter' => array("ID" => $ID),
		));
		if ($arData = $rsData->Fetch())
        {
            foreach($arFields as $key => $value)
                $arData[$key] = $value;

			PskRatesInEuroTable::update($ID, $arData);
        }
        else
            $lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
    }
}

// обработка одиночных и групповых действий
if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
	/*
    // если выбрано "Для всех элементов"
    if($_REQUEST['action_target']=='selected')
    {
        $cData = new CRubric;
        $rsData = $cData->GetList(array($by=>$order), $arFilter);
        while($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    // пройдем по списку элементов
    foreach($arID as $ID)
    {
        if(strlen($ID)<=0)
            continue;
       	$ID = IntVal($ID);
        
        // для каждого элемента совершим требуемое действие
        switch($_REQUEST['action'])
        {
        // удаление
        case "delete":
            @set_time_limit(0);
            $DB->StartTransaction();
            if(!CRubric::Delete($ID))
            {
                $DB->Rollback();
                $lAdmin->AddGroupError(GetMessage("rub_del_err"), $ID);
            }
            $DB->Commit();
            break;
        
        // активация/деактивация
        case "activate":
        case "deactivate":
            $cData = new CRubric;
            if(($rsData = $cData->GetByID($ID)) && ($arFields = $rsData->Fetch()))
            {
                $arFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");
                if(!$cData->Update($ID, $arFields))
                    $lAdmin->AddGroupError(GetMessage("rub_save_error").$cData->LAST_ERROR, $ID);
            }
            else
                $lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
            break;
        }

    }
	 */
}

// выберем список элементов
$order = mb_strtoupper($order);
$rsData = PskRatesInEuroTable::getList(array(
	'order' => array($by => $order), 
	'filter' => $arFilter,
));

// преобразуем список в экземпляр класса CAdminResult
$rsData = new CAdminResult($rsData, $sTableID);

// аналогично CDBResult инициализируем постраничную навигацию.
$rsData->NavStart();

// отправим вывод переключателя страниц в основной объект $lAdmin
$lAdmin->NavText($rsData->GetNavPrint("Страницы с по: "));


$lAdmin->AddHeaders(array(
	array(
		"id"       => "ID",
		"content"  => "ID",
		"sort"     => "ID",
		"default"  => true,
	),
	array(
		"id"       => "WEIGHT",
		"content"  => Loc::getMessage("DAVIDOK95_PSK_RATESINEURO_WEIGHT"),
		"sort"     => "WEIGHT",
		"default"  =>true,
	),
	array(
		"id"       => "ZONE",
		"content"  => Loc::getMessage("DAVIDOK95_PSK_RATESINEURO_ZONE"),
		"sort"     => "ZONE",
		"default"  => true,
	),
	array(
		"id"       => "PRICE",
		"content"  => Loc::getMessage("DAVIDOK95_PSK_RATESINEURO_PRICE"),
		"sort"     => "PRICE",
		"default"  => true,
	),
));


while($arRes = $rsData->NavNext(true, "f_"))
{

	// создаем строку. результат - экземпляр класса CAdminListRow
	$row =& $lAdmin->AddRow($f_ID, $arRes); 

	// далее настроим отображение значений при просмотре и редактировании списка

	//$row->AddInputField("WEIGHT", array("size"=>20));
	//$row->AddInputField("ZONE", array("size"=>20));
	$row->AddInputField("PRICE", array("size"=>20));

	// сформируем контекстное меню
	$arActions = Array();

	// редактирование элемента
	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>GetMessage("rub_edit"),
		"ACTION"=>$lAdmin->ActionRedirect("rubric_edit.php?ID=".$f_ID)
	);

	// удаление элемента
	if ($POST_RIGHT>="W")
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("rub_del"),
			"ACTION"=>"if(confirm('".GetMessage('rub_del_conf')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);

	// вставим разделитель
	$arActions[] = array("SEPARATOR"=>true);

	// проверка шаблона для автогенерируемых рассылок
	if (strlen($f_TEMPLATE)>0 && $f_AUTO=="Y")
		$arActions[] = array(
			"ICON"=>"",
			"TEXT"=>GetMessage("rub_check"),
			"ACTION"=>$lAdmin->ActionRedirect("template_test.php?ID=".$f_ID)
		);

	// если последний элемент - разделитель, почистим мусор.
	if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
		unset($arActions[count($arActions)-1]);

	// применим контекстное меню к строке
	$row->AddActions($arActions);
}

// резюме таблицы
$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), // кол-во элементов
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
	)
);


// групповые действия
$lAdmin->AddGroupActionTable(Array(
	//"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"), // удалить выбранные элементы
	//"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"), // активировать выбранные элементы
	//"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // деактивировать выбранные элементы
));


// альтернативный вывод
$lAdmin->CheckListMode();

// set title
$APPLICATION->SetTitle(Loc::getMessage("DAVIDOK95_PSK_RATESINEURO_TITLE"));
?>


<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог
?>


<?
// создадим объект фильтра
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"ID",
		Loc::GetMessage("DAVIDOK95_PSK_RATESINEURO_WEIGHT"),
		Loc::getMessage("DAVIDOK95_PSK_RATESINEURO_ZONE"),
		Loc::getMessage("DAVIDOK95_PSK_RATESINEURO_PRICE"),
	)
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
	<? $oFilter->Begin(); ?>
	<tr>
		<td><?= "ID" ?>:</td>
		<td>
			<input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
		</td>
	</tr>
	<tr>
		<td><?= Loc::GetMessage("DAVIDOK95_PSK_RATESINEURO_WEIGHT") ?>:</td>
		<td>
			<input type="text" name="find_weight" size="47" value="<?echo htmlspecialchars($find_weight)?>">
		</td>
	</tr>
	<tr>
		<td><?= Loc::GetMessage("DAVIDOK95_PSK_RATESINEURO_ZONE") ?>:</td>
		<td>
			<input type="text" name="find_zone" size="47" value="<?echo htmlspecialchars($find_zone)?>">
		</td>
	</tr>
	<tr>
		<td><?= Loc::GetMessage("DAVIDOK95_PSK_RATESINEURO_PRICE") ?>:</td>
		<td>
			<input type="text" name="find_price" size="47" value="<?echo htmlspecialchars($find_price)?>">
		</td>
	</tr>

	<?
	$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
	$oFilter->End();
	?>
</form>

<?
// выведем таблицу списка элементов
$lAdmin->DisplayList();
?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
