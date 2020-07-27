<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arComponentDescription = array(
	'NAME' => Loc::getMessage('CUSTOM_CRM_LEADS_NAME'),
	'DESCRIPTION' => Loc::getMessage('CUSTOM_CRM_LEADS_DESCRIPTION'),
	'ICON' => '',
	'SORT' => 100,
	'COMPLEX' => 'N',
	'PATH' => array(
		'ID' => 'custom_crm',
		'NAME' => Loc::getMessage('CUSTOM_CRM')
	),
	'CACHE_PATH' => 'Y'	
);
?>
