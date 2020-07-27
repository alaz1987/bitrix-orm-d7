<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Crm\LeadTable;
use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;

$modules = array("crm", "iblock");

// проверка на установку модулей
foreach ($modules as $mod) {
    if(!Loader::includeModule($mod)) {
        $mod = strtoupper($mod);
        ShowError(Loc::getMessage("MODULE_{$mod}_NOT_INSTALLED"));
        return false;
    }
}

// проверяем параметры на валидацию
$this->arParams["LEADS_COUNT"] = intval($this->arParams["LEADS_COUNT"]);
$this->arParams["IBLOCK_ELEMENTS_COUNT"] = intval($this->arParams["IBLOCK_ELEMENTS_COUNT"]);
$this->arParams["IBLOCK_ID"] = intval($this->arParams["IBLOCK_ID"]);

if (empty($this->arParams["IBLOCK_ID"])) {
    ShowError(Loc::getMessage("IBLOCK_ID_INVALID"));
    return false;
}

// проверяем сущестование инфоблока в системе
$result = IblockTable::getById($this->arParams["IBLOCK_ID"])->fetch();

if (empty($result)) {
    ShowError(Loc::getMessage("IBLOCK_NOT_FOUND", array("#ID#" => $this->arParams["IBLOCK_ID"])));
    return false;
}

$this->arResult["IBLOCK"] = $result;

// значение параметров по умолчанию
if ($this->arParams["LEADS_COUNT"] <= 0) {
    $this->arParams["LEADS_COUNT"] = 10;
}
if ($this->arParams["IBLOCK_ELEMENTS_COUNT"] <= 0) {
    $this->arParams["IBLOCK_ELEMENTS_COUNT"] = 10;
}


/********************* CRM leads *********************/

// параметры для getList -> выбираем лиды
$parameters = array(
    "select"  => array("*"),
    "filter"  => array(
        ">=DATE_CREATE" => date("d.m.Y")." 00:00:00",
        "<=DATE_CREATE" => date("d.m.Y")." 23:59:59"
    ),
    "group"   => array(),
    "order"   => array("ID" => "ASC"),
    "limit"   => $this->arParams["LEADS_COUNT"],
    "offset"  => array(),
    "runtime" => array()
);

// делаем запрос базу через ORM - список лидов
$this->arResult["LEADS"]["ITEMS"] = LeadTable::getList($parameters)->fetchAll();
$this->arResult["LEADS"]["COLUMNS"] = array_keys(current($this->arResult["LEADS"]["ITEMS"]));


/********************* IBlock elements *********************/

// получаем символьный код API
$entityDataClass = Iblock::wakeUp($this->arParams["IBLOCK_ID"])->getEntityDataClass();

if (!empty($entityDataClass)) {
    // основные поля элемента инфоблока
    $this->arResult["IBLOCK_ELEMENTS"]["COLUMNS"] = array(
        "ID"            => "ID",
        "NAME"          => "Название",
        "SORT"          => "Сортировка",
        "PREVIEW_TEXT"  => "Текст анонса",
        "DETAIL_TEXT"   => "Детальный текст"
    );

    $arProperties = array();
    $arFields     = array_keys($this->arResult["IBLOCK_ELEMENTS"]["COLUMNS"]);

    // получим свойства инфоблока
    $parameters = array(
        "select" => array("CODE", "NAME"),
        "filter" => array(
            "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "ACTIVE"    => "Y",
            "!=CODE"    => false
        )
    );
    $rsProperty = PropertyTable::getList($parameters);

    while($arProperty = $rsProperty->fetch()) {
        $arProperties[] = $arProperty["CODE"];
        $this->arResult["IBLOCK_ELEMENTS"]["COLUMNS"][$arProperty["CODE"]] = $arProperty["NAME"];
    }

    // получаем коллекцию элементов инфоблока
    $parameters = array(
        "select"  => array_merge($arFields, $arProperties),
        "filter"  => array(
            "=IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "=ACTIVE" => "Y"
        ),
        "order"   => array("ID" => "ASC"),
        "limit"   => $this->arParams["IBLOCK_ELEMENTS_COUNT"]
    );

    $elements = $entityDataClass::getList($parameters)->fetchCollection();
    
    function getMethodName($field) {
        $methodPhrases = explode("_", $field);
            
        $methodPhrases = array_map(function($part) {
            return strtoupper(substr($part, 0, 1)).strtolower(substr($part, 1));
        }, $methodPhrases);

        $methodName = implode("", $methodPhrases);
        $methodName = "get{$methodName}";

        return $methodName;
    }

    foreach ($elements as $elem) {
        $data = array();
        
        foreach ($arFields as $field) {
            $methodName = getMethodName($field);
            $data[$field] = $elem->$methodName();
        }
        foreach ($arProperties as $field) {
            $methodName = getMethodName($field);
            $prop       = $elem->$methodName();

            if (!empty($prop)) {
                $data[$field] = $prop->getValue();
            }
        }

        $this->arResult["IBLOCK_ELEMENTS"]["ITEMS"][] = $data;
    }
} else {
    ShowError(Loc::getMessage("IBLOCK_API_CODE_EMPTY", array("#ID#" => $this->arParams["IBLOCK_ID"])));
}

$this->IncludeComponentTemplate();
?>