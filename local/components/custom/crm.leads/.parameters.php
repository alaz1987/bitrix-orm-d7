<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\IblockTable;

$request = Application::getInstance()->getContext()->getRequest();
$modules = array("crm", "iblock");

// проверка на установку модулей
foreach ($modules as $mod) {
    if(!Loader::includeModule($mod)) {
        $mod = strtoupper($mod);
        ShowError(Loc::getMessage("MODULE_{$mod}_NOT_INSTALLED"));
        return false;
    }
}

// iblock type
$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-" => " "));

// iblock id
$arIBlocks = array();
$parameters = array(
    'select'  => array("ID", "NAME"),
    'filter'  => array_filter(array(
		"=LID"  		  => $request->get("src_site"),
		"=IBLOCK_TYPE_ID" => (
			$arCurrentValues["IBLOCK_TYPE"] != "-"
			?
			$arCurrentValues["IBLOCK_TYPE"]
			:
			""
		)
	)),
    'group'   => array(),
    'order'   => array("SORT" => "ASC"),
    'limit'   => array(),
    'offset'  => array(),
    'runtime' => array()
);
$dbIblock = IblockTable::getList($parameters);

while($row = $dbIblock->Fetch()) {
	$arIBlocks[$row["ID"]] = $row["NAME"];
}

$arComponentParameters = array(
	'PARAMETERS' => array(		
		'LEADS_COUNT' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('CUSTOM_CRM_PARAMS_LEADS_COUNT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '10'
		),
		'IBLOCK_ELEMENTS_COUNT' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('CUSTOM_CRM_PARAMS_IBLOCK_ELEMENTS_COUNT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '10'
		),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CUSTOM_CRM_PARAMS_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"REFRESH" => "Y"
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CUSTOM_CRM_PARAMS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"REFRESH" => "Y"
		)
	)
);
?>
