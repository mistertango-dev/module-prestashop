<?php

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/mttools.php');
require_once(dirname(__FILE__) . '/mtpayment.php');

$hash = Tools::getValue('hash');

/**
 * Debug purpose only.
 */
//$hash = 'gOjTKaYmeTyoOQ\/tMa1eFXIg\/EUkLRGfScpf4NugyF1b5N7KJXh8D86KLdcIv0WiIBjpvELcNUlpD7gFwaVedQKnz20Er9FxeTHOm1Ry5+laD+f3xgof3jshhwh\/JTbOwb0EFkzxEQderYrzV0r6amdrl4Vnxm3h+VRQYesv7Ll9q9Mw\/mhHbdNlcP4MVZKhQ5baY+Y1nWM4Jzbi8me\/nyvHUOE981zQ\/5WEudCHYFR22LAmVCdmZ+dGwO\/hhYs5ZIFHgJDBf67d\/ALoUiwUhvaLBHdiPn09Cx06ft2m1uzZiNt\/RuNoyaYEqjs7Rw4LkweqD80E0mK0cYZiPRy2ZeeMyj1mvx\/rGKcVBvEX60WiafIMzqCV91EfVYO\/dwDH8o\/zI78IRWZxaGzMccRfKBZ\/pTU6PB7kqUtFkymBH\/c3IVrCASklcay2G0NyiKNy30jvbOdfZG4USck3QzrGFyuMQZ6Gpvi1Bg2PmK2OFGx+HMvvLjflRAgVl8sUK9rd4ILPTG2+bCsiHCUmi3Myo8slC1FWH4h2zxGkQn9lGiALjYu3wGn6JxYRe45vdQHsWUoKGFJv\/kxYsHkXiV\/RXsKq2HEpyyfwycDaAhKWp+fFBe\/ikrhp1azjH5cIrLVQ1acjzC8pACwhfyjz4TsB5Uk2ZwvCQNDeWQN96mn4ncjrn+9ZtjIGJgzPgrQ4SeF0O0wq15tWl6oHnrWJ1ICN5xOKWnxO6jxiycDA1kaVWxrEz33JFtRpYj\/70NPLK\/0Z5EOwDwtWTsd77b3XBzVFpA==';

if ($hash !== false) {
    $data = Tools::jsonDecode(MTTools::decrypt($hash, Configuration::get(MTConfiguration::NAME_SECRET_KEY)));

    if (isset($data)) {
        $data->custom = isset($data->custom) ? Tools::jsonDecode($data->custom) : null;
    }

    if (!isset($data->custom) && !isset($data->custom->description)) {
        die();
    }

    /*
     * Debug purpose only
     */
    //$data->custom->description = '40_1450273575';
    //$data->custom->data->amount = '19.85';

    $message = '';
    $transaction = explode('_', $data->custom->description);

    if (count($transaction) != 2) {
        die();
    }

    if (MTCallbacks::exists($data->callback_uuid)) {
        die('OK');
    }

    $success = false;

    try {
        $id_cart = $transaction[0];
        $id_transaction = implode('_', $transaction);

        $success = MTOrders::close(
            $id_transaction,
            $data->custom->data->amount
        );
    } catch (Exception $e) {
        die();
    }

    if ($success) {
        MTCallbacks::insert($data->callback_uuid, $data->custom->description, $data->custom->data->amount);
        die('OK');
    }
}

die();
