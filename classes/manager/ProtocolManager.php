<?php

namespace HeimrichHannot\Privacy\Manager;

use Contao\BackendUser;
use Contao\ContentElement;
use Contao\Controller;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Module;
use Contao\ModuleModel;
use Contao\System;
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

    public function addEntry($type, $archive, array $data, $packageName = '')
    {
        if (($protocolArchive = ProtocolArchiveModel::findByPk($archive)) === null)
        {
            return false;
        }

        $allowedPersonalFields = deserialize($protocolArchive->personalFields, true);
        $allowedCodeFields = deserialize($protocolArchive->codeFields, true);

        Controller::loadDataContainer('tl_privacy_protocol_entry');

        $dca = &$GLOBALS['TL_DCA']['tl_privacy_protocol_entry'];

        $protocolEntry         = new ProtocolEntryModel();
        $protocolEntry->tstamp = $protocolEntry->dateAdded = time();
        $protocolEntry->pid    = $archive;
        $protocolEntry->type   = $type;
        $stackTrace            = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

        foreach ($dca['fields'] as $field => $fieldData)
        {
            if (!in_array($field, $allowedPersonalFields) && isset($fieldData['eval']['personalField']) && $fieldData['eval']['personalField'])
            {
                continue;
            }

            if ((!in_array($field, $allowedCodeFields) || !$protocolArchive->addCodeProtocol) && isset($fieldData['eval']['codeField']) && $fieldData['eval']['codeField'])
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
                case 'member':
                    if (TL_MODE == 'FE' && FE_USER_LOGGED_IN)
                    {
                        $protocolEntry->member = FrontendUser::getInstance()->id;
                    }
                    break;
                case 'user':
                    if (TL_MODE == 'BE' && BE_USER_LOGGED_IN)
                    {
                        $protocolEntry->user = BackendUser::getInstance()->id;
                    }
                    break;
                case 'cmsScope':
                    if (TL_MODE == 'FE')
                    {
                        $protocolEntry->cmsScope = ProtocolEntry::CMS_SCOPE_FRONTEND;
                    }
                    elseif (TL_MODE == 'BE')
                    {
                        $protocolEntry->cmsScope = ProtocolEntry::CMS_SCOPE_BACKEND;
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

                    $composerLock = file_get_contents(TL_ROOT . '/composer/composer.lock');

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
                    if (count($stackTrace) > 1)
                    {
                        $protocolEntry->codeFile = $stackTrace[1]['file'];
                    }
                    break;
                case 'codeLine':
                    if (count($stackTrace) > 1)
                    {
                        $protocolEntry->codeLine = $stackTrace[1]['line'];
                    }
                    break;
                case 'codeFunction':
                    if (count($stackTrace) > 1)
                    {
                        $protocolEntry->codeFunction = $stackTrace[1]['function'];
                    }
                    break;
                case 'codeStacktrace':
                    $protocolEntry->codeStacktrace = (new \Exception())->getTraceAsString();
                    break;
            }

            // $data always has the highest priority
            if (isset($data[$field]))
            {
                $protocolEntry->{$field} = $data[$field];
            }
        }

        $protocolEntry->save();

        return $protocolEntry;
    }
}