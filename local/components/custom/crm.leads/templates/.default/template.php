<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if (!empty($arResult["LEADS"]["ITEMS"])):?>
    <h3>Лиды</h3>
    <table class="table">
        <thead>
            <?foreach ($arResult["LEADS"]["COLUMNS"] as $code => $val):?>
                <th><?echo $val;?></th>
            <?endforeach?>
        </thead>
        <tbody>
            <?foreach ($arResult["LEADS"]["ITEMS"] as $lead):?>
                <tr>
                    <?foreach ($arResult["LEADS"]["COLUMNS"] as $code):?>
                        <td><?=$lead[$code]?></td>
                    <?endforeach?>   
                </tr>
            <?endforeach?>
        </tbody>
    </table>
<?endif?>

<br>

<?if (!empty($arResult["IBLOCK_ELEMENTS"]["ITEMS"])):?>
    <h3>Элементы инфоблока &laquo;<?=$arResult["IBLOCK"]["NAME"]?>&raquo;</h3>
    <table class="table">
        <thead>
            <?foreach ($arResult["IBLOCK_ELEMENTS"]["COLUMNS"] as $val):?>
                <th><?=$val?></th>
            <?endforeach?>
        </thead>
        <tbody>
            <?foreach ($arResult["IBLOCK_ELEMENTS"]["ITEMS"] as $elem):?>
                <tr>
                    <?foreach ($arResult["IBLOCK_ELEMENTS"]["COLUMNS"] as $code => $val):?>
                        <td><?=$elem[$code]?></td>
                    <?endforeach?>
                </tr>
            <?endforeach?>
        </tbody>
    </table>
<?endif?>