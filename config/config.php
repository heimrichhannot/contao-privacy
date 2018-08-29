<?php

/**
 * Backend modules
 */
array_insert(
    $GLOBALS['BE_MOD'],
    1,
    [
        'privacy' => [
            'privacy_opt_in' => [
                'callback' => 'HeimrichHannot\Privacy\Module\ModuleBackendOptIn',
                'icon'     => 'system/modules/privacy/assets/img/icon_email.png',
            ],
            'protocols'      => [
                'tables' => ['tl_privacy_protocol_archive', 'tl_privacy_protocol_entry'],
                'icon'   => 'system/modules/privacy/assets/img/icon_protocol.png',
            ],
        ],
    ]
);

if (class_exists('HeimrichHannot\Exporter\ModuleExporter')) {
    $GLOBALS['BE_MOD']['privacy']['protocols']['export_csv'] = \HeimrichHannot\Exporter\ModuleExporter::getBackendModule();
    $GLOBALS['BE_MOD']['privacy']['protocols']['export_xls'] = \HeimrichHannot\Exporter\ModuleExporter::getBackendModule();
}

/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['privacy'][\HeimrichHannot\Privacy\Backend\Module::PROTOCOL_ENTRY_EDITOR] = 'HeimrichHannot\Privacy\Module\ModuleProtocolEntryEditor';

/**
 * Assets
 */
if (TL_MODE == 'BE') {
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
$GLOBALS['TL_HOOKS']['loadDataContainer']['privacy_initProtocolCallbacks'] = ['HeimrichHannot\Privacy\EventListener\HookListener', 'initProtocolCallbacks'];
$GLOBALS['TL_HOOKS']['replaceInsertTags']['privacy_addInsertTags']         = ['HeimrichHannot\Privacy\EventListener\HookListener', 'addInsertTags'];

/**
 * Notifications
 */
$backendOptInType                 = \HeimrichHannot\Haste\Dca\Notification::getNewNotificationTypeArray(true);
$backendOptInType['email_text'][] = 'opt_in_url';
$backendOptInType['email_text'][] = 'salutation_submission';
$backendOptInType['email_html'][] = 'opt_in_url';
$backendOptInType['email_html'][] = 'salutation_submission';

foreach ($backendOptInType as $strField => $arrTokens) {
    $backendOptInType[$strField] = array_unique(array_merge(['form_*'], $arrTokens));
}

\HeimrichHannot\Haste\Dca\Notification::activateType(
    \HeimrichHannot\Privacy\Backend\Notification::NOTIFICATION_TYPE_PRIVACY,
    \HeimrichHannot\Privacy\Backend\Notification::NOTIFICATION_TYPE_PRIVACY_OPT_IN_FORM,
    $backendOptInType
);

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'privacy_protocols';
$GLOBALS['TL_PERMISSIONS'][] = 'privacy_protocolp';