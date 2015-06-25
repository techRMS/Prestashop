<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * upgrades version 1.2 to 1.3
 *
 * no longer requires users to manually adjust hooks
 * adds header hook for css file
 *
 * @param $object
 *
 * @return bool
 */
function upgrade_module_1_3($object)
{
    return ( $object->registerHook('displayPayment') && $object->registerHook('displayPaymentReturn') &&
             $object->registerHook('header') );
}
