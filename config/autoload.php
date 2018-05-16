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
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'HeimrichHannot\Privacy\Model\ProtocolArchiveModel' => 'system/modules/privacy/classes/models/ProtocolArchiveModel.php',
	'HeimrichHannot\Privacy\Model\ProtocolEntryModel'   => 'system/modules/privacy/classes/models/ProtocolEntryModel.php',
	'HeimrichHannot\Privacy\Backend\ProtocolArchive'    => 'system/modules/privacy/classes/backend/ProtocolArchive.php',
	'HeimrichHannot\Privacy\Backend\ProtocolEntry'      => 'system/modules/privacy/classes/backend/ProtocolEntry.php',
));
