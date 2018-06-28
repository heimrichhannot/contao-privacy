<?php

namespace HeimrichHannot\Privacy\Manager;

use Contao\BackendUser;
use Contao\Config;
use Contao\ContentElement;
use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\DcaExtractor;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Model;
use Contao\Module;
use Contao\ModuleModel;
use Contao\System;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\StringUtil;
use HeimrichHannot\Privacy\Backend\ProtocolEntry;
use HeimrichHannot\Privacy\Model\ProtocolArchiveModel;
use HeimrichHannot\Privacy\Model\ProtocolEntryModel;

class ProtocolManager
{
    public function addEntryFromContentElement($type, $archive, array $data, ContentElement $element, $packageName = '')
    {
        $data['element']     = $element->id;
        $data['elementType'] = $element->type;

        $this->addEntry($type, $archive, $data, $packageName);
    }

    /**
     * Adds a new protocol entry from the scope of a module
     *
     * @param string             $type
     * @param int                $archive
     * @param array              $data
     * @param Module|ModuleModel $module
     * @param string             $packageName
     */
    public function addEntryFromModule($type, $archive, array $data, $module, $packageName = '')
    {
        $data['module']     = $module->id;
        $data['moduleType'] = $module->type;
        $data['moduleName'] = $module->name;

        $this->addEntry($type, $archive, $data, $packageName);
    }

    public function addEntry($type, $archive, array $data, $packageName = '', $skipFields = ['id', 'tstamp', 'dateAdded', 'pid', 'type'])
    {
        if (($protocolArchive = ProtocolArchiveModel::findByPk($archive)) === null)
        {
            return false;
        }

        $allowedPersonalFields = deserialize($protocolArchive->personalFields, true);
        $allowedCodeFields     = deserialize($protocolArchive->codeFields, true);

        Controller::loadDataContainer('tl_privacy_protocol_entry');

        $dca = &$GLOBALS['TL_DCA']['tl_privacy_protocol_entry'];

        $protocolEntry         = new ProtocolEntryModel();
        $protocolEntry->tstamp = $protocolEntry->dateAdded = time();
        $protocolEntry->pid    = $archive;
        $protocolEntry->type   = $type;
        $stackTrace            = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);
        $relevantStackEntry    = [];

        // compute stacktrace entry containing the relevant function call
        if (!empty($stackTrace))
        {
            $classMethods = get_class_methods('HeimrichHannot\Privacy\Manager\ProtocolManager');

            foreach ($stackTrace as $index => $entry)
            {
                if (!StringUtil::endsWith($entry['file'], 'ProtocolManager.php') || !in_array($entry['function'], $classMethods))
                {
                    $relevantStackEntry = $entry;

                    if (isset($stackTrace[$index + 1]['function']))
                    {
                        $relevantStackEntry['function'] = $stackTrace[$index + 1]['function'];
                    }

                    break;
                }
            }
        }

        foreach ($dca['fields'] as $field => $fieldData)
        {
            if (!in_array($field, $allowedPersonalFields) && isset($fieldData['eval']['personalField']) && $fieldData['eval']['personalField'])
            {
                continue;
            }

            if ((!in_array($field, $allowedCodeFields) || !$protocolArchive->addCodeProtocol) && isset($fieldData['eval']['codeField'])
                && $fieldData['eval']['codeField'])
            {
                continue;
            }

            switch ($field)
            {
                case 'ip':
                    if (Environment::get('remoteAddr'))
                    {
                        $protocolEntry->ip = System::anonymizeIp(Environment::get('ip'));
                    }
                    break;
                case 'cmsScope':
                    if (TL_MODE == 'FE')
                    {
                        $protocolEntry->cmsScope   = ProtocolEntry::CMS_SCOPE_FRONTEND;

                        if (null !== ($member = FrontendUser::getInstance()) && $member->id)
                        {
                            $protocolEntry->authorType = General::AUTHOR_TYPE_MEMBER;
                            $protocolEntry->author     = $member->id;
                        }
                    }
                    elseif (TL_MODE == 'BE')
                    {
                        $protocolEntry->cmsScope   = ProtocolEntry::CMS_SCOPE_BACKEND;

                        if (null !== ($user = BackendUser::getInstance()) && $user->id)
                        {
                            $protocolEntry->authorType = General::AUTHOR_TYPE_USER;
                            $protocolEntry->author     = $user->id;
                        }
                    }
                    break;
                case 'url':
                    $protocolEntry->url = \Environment::get('url') . '/' . \Environment::get('request');
                    break;
                case 'bundle':
                    $protocolEntry->bundle = $packageName;
                    break;
                case 'bundleVersion':
                    if (!$packageName)
                    {
                        continue 2;
                    }

                    $path = TL_ROOT . '/composer/composer.lock';

                    if (!file_exists($path))
                    {
                        $path = TL_ROOT . '/composer.lock';
                    }

                    if (!file_exists($path))
                    {
                        continue 2;
                    }

                    $composerLock = file_get_contents($path);

                    if (!$composerLock)
                    {
                        continue 2;
                    }

                    try
                    {
                        $composerLock = json_decode($composerLock, true);

                        foreach ($composerLock['packages'] as $package)
                        {
                            if (isset($package['name']) && $package['name'] === $packageName)
                            {
                                if (isset($package['version']))
                                {
                                    $protocolEntry->bundleVersion = $package['version'];
                                }

                                break;
                            }
                        }
                    } catch (\Exception $e)
                    {
                        // silently fail
                    }
                    break;
                case 'codeFile':
                    if (!empty($relevantStackEntry))
                    {
                        $protocolEntry->codeFile = $relevantStackEntry['file'];
                    }
                    break;
                case 'codeLine':
                    if (!empty($relevantStackEntry))
                    {
                        $protocolEntry->codeLine = $relevantStackEntry['line'];
                    }
                    break;
                case 'codeFunction':
                    if (!empty($relevantStackEntry))
                    {
                        $protocolEntry->codeFunction = $relevantStackEntry['function'];
                    }
                    break;
                case 'codeStacktrace':
                    $protocolEntry->codeStacktrace = (new \Exception())->getTraceAsString();
                    break;
            }

            // $data always has the highest priority
            if (isset($data[$field]) && !in_array($field, $skipFields))
            {
                $protocolEntry->{$field} = $data[$field];
            }
        }

        $protocolEntry->save();

        // set reference field
        if ($protocolArchive->setReferenceFieldOnChange)
        {
            $modelClass = Model::getClassFromTable($protocolArchive->referenceFieldTable);

            if (class_exists($modelClass))
            {
                $instance = $modelClass::findBy([$protocolArchive->referenceFieldProtocolForeignKey . '=?'], [$protocolEntry->{$protocolArchive->referenceFieldForeignKey}]);

                if (null === $instance && $protocolArchive->createInstanceOnChange)
                {
                    $instance = new $modelClass();
                    $instance->tstamp = $instance->dateAdded = time();

                    foreach ($data as $field => $value)
                    {
                        $instance->{$field} = $value;
                    }

                    if (isset($GLOBALS['TL_HOOKS']['privacy_initReferenceModelOnProtocolChange']) && \is_array($GLOBALS['TL_HOOKS']['privacy_initReferenceModelOnProtocolChange']))
                    {
                        foreach ($GLOBALS['TL_HOOKS']['privacy_initReferenceModelOnProtocolChange'] as $callback)
                        {
                            if (\is_array($callback))
                            {
                                $this->import($callback[0]);
                                $this->{$callback[0]}->{$callback[1]}($instance, $protocolEntry, $data);
                            }
                            elseif (\is_callable($callback))
                            {
                                $callback($instance, $protocolEntry, $data);
                            }
                        }
                    }
                }

                if (null !== $instance)
                {
                    $instance->{$protocolArchive->referenceField} = $protocolEntry->type;

                    $instance->save();
                }
            }
        }

        return $protocolEntry;
    }

    public function getSelectorFieldDca()
    {
        return [
            'label'     => $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['addPrivacyProtocolEntry'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ];
    }

    public function getArchiveFieldDca()
    {
        return [
            'label'      => $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['privacyProtocolEntryArchive'],
            'exclude'    => true,
            'filter'     => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_privacy_protocol_archive.title',
            'eval'       => ['tl_class' => 'w50 clr', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
            'sql'        => "int(10) unsigned NOT NULL default '0'"
        ];
    }

    public function getTypeFieldDca()
    {
        System::loadLanguageFile('tl_privacy_protocol_entry');

        return [
            'label'     => $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['privacyProtocolEntryType'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\Privacy\Backend\ProtocolEntry::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
            'sql'       => "varchar(32) NOT NULL default ''"
        ];
    }

    public function getDescriptionFieldDca()
    {
        return [
            'label'     => $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['privacyProtocolEntryDescription'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => ['tl_class' => 'long clr'],
            'sql'       => "text NULL"
        ];
    }

    public function getFieldMappingFieldDca($tableField)
    {
        return [
            'label'     => $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['privacyProtocolFieldMapping'],
            'inputType' => 'multiColumnEditor',
            'eval'      => [
                'tl_class'          => 'long clr',
                'multiColumnEditor' => [
                    'minRowCount' => 0,
                    'fields'      => [
                        'entityField'   => [
                            'label'            => &$GLOBALS['TL_LANG']['MSC']['huhPrivacy']['privacyProtocolFieldMapping_entityField'],
                            'inputType'        => 'select',
                            'options_callback' => function(DataContainer $dc) use ($tableField) {
                                if (!$dc->activeRecord->{$tableField})
                                {
                                    return [];
                                }

                                return General::getFields($dc->activeRecord->{$tableField}, false);
                            },
                            'exclude'          => true,
                            'eval'             => [
                                'includeBlankOption' => true,
                                'chosen'             => true,
                                'tl_class'           => 'w50',
                                'mandatory'          => true,
                                'style'              => 'width: 400px'
                            ],
                        ],
                        'protocolField' => [
                            'label'            => &$GLOBALS['TL_LANG']['MSC']['huhPrivacy']['privacyProtocolFieldMapping_protocolField'],
                            'inputType'        => 'select',
                            'options_callback' => ['HeimrichHannot\Privacy\Backend\ProtocolEntry', 'getFieldsAsOptions'],
                            'exclude'          => true,
                            'eval'             => [
                                'includeBlankOption' => true,
                                'chosen'             => true,
                                'tl_class'           => 'w50',
                                'mandatory'          => true,
                                'style'              => 'width: 400px'
                            ],
                        ]
                    ],
                ],
            ],
            'sql'       => "blob NULL",
        ];
    }
}