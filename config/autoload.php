<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2018 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces([
    'HeimrichHannot',
]);


/**
 * Register the classes
 */
ClassLoader::addClasses([
    // Classes
    'HeimrichHannot\Privacy\Manager\ProtocolManager'          => 'system/modules/privacy/classes/manager/ProtocolManager.php',
    'HeimrichHannot\Privacy\Model\ProtocolArchiveModel'       => 'system/modules/privacy/classes/models/ProtocolArchiveModel.php',
    'HeimrichHannot\Privacy\Model\ProtocolEntryModel'         => 'system/modules/privacy/classes/models/ProtocolEntryModel.php',
    'HeimrichHannot\Privacy\Backend\Backend'                  => 'system/modules/privacy/classes/backend/Backend.php',
    'HeimrichHannot\Privacy\Backend\Notification'             => 'system/modules/privacy/classes/backend/Notification.php',
    'HeimrichHannot\Privacy\Backend\ProtocolArchive'          => 'system/modules/privacy/classes/backend/ProtocolArchive.php',
    'HeimrichHannot\Privacy\Backend\ProtocolEntry'            => 'system/modules/privacy/classes/backend/ProtocolEntry.php',
    'HeimrichHannot\Privacy\Backend\Module'                   => 'system/modules/privacy/classes/backend/Module.php',
    'HeimrichHannot\Privacy\Privacy'                          => 'system/modules/privacy/classes/Privacy.php',
    'HeimrichHannot\Privacy\EventListener\HookListener'       => 'system/modules/privacy/classes/event_listener/HookListener.php',
    'HeimrichHannot\Privacy\Form\ProtocolEntryForm'           => 'system/modules/privacy/classes/form/ProtocolEntryForm.php',
    'HeimrichHannot\Privacy\Util\ProtocolUtil'                => 'system/modules/privacy/classes/util/ProtocolUtil.php',
    'HeimrichHannot\Privacy\Module\ModuleProtocolEntryEditor' => 'system/modules/privacy/modules/ModuleProtocolEntryEditor.php',
    'HeimrichHannot\Privacy\Module\ModuleBackendOptIn'        => 'system/modules/privacy/modules/ModuleBackendOptIn.php',
]);

TemplateLoader::addFiles([
    'mod_protocol_entry_editor' => 'system/modules/privacy/templates',
    'be_privacy_opt_in'         => 'system/modules/privacy/templates',
]);