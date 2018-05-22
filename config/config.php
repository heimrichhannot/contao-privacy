<?php

/**
 * Backend modules
 */
array_insert(
    $GLOBALS['BE_MOD'],
    1,
    [
        'privacy' => [
            'protocols' => [
                'tables' => ['tl_privacy_protocol_archive', 'tl_privacy_protocol_entry'],
                'icon' => 'system/modules/privacy/assets/img/icon_protocol.png'
            ]
        ]
    ]
);

/**
 * Assets
 */
if (TL_MODE == 'BE')
{
    // css
    $GLOBALS['TL_CSS']['privacy'] = 'system/modules/privacy/assets/css/privacy.be.min.css|static';
}

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_privacy_protocol_archive'] = 'HeimrichHannot\Privacy\Model\ProtocolArchiveModel';
$GLOBALS['TL_MODELS']['tl_privacy_protocol_entry']   = 'HeimrichHannot\Privacy\Model\ProtocolEntryModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer']['initProtocolCallbacks'] = ['HeimrichHannot\Privacy\Manager\ProtocolManager', 'initProtocolCallbacks'];

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'privacy_protocols';
$GLOBALS['TL_PERMISSIONS'][] = 'privacy_protocolp';