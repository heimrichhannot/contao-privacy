<?php

namespace HeimrichHannot\Privacy\Backend;

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\System;
use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\Privacy\Model\ProtocolArchiveModel;
use HeimrichHannot\Privacy\Model\ProtocolEntryModel;

class ProtocolEntry extends \Contao\Backend
{
    const TYPE_FIRST_OPT_IN   = 'first_opt_in';
    const TYPE_SECOND_OPT_IN  = 'second_opt_in';
    const TYPE_FIRST_OPT_OUT  = 'first_opt_out';
    const TYPE_SECOND_OPT_OUT = 'second_opt_out';
    const TYPE_OPT_OUT        = 'opt_out';
    const TYPE_CREATE         = 'create';
    const TYPE_UPDATE         = 'update';
    const TYPE_DELETE         = 'delete';

    const TYPES = [
        self::TYPE_FIRST_OPT_IN,
        self::TYPE_SECOND_OPT_IN,
        self::TYPE_FIRST_OPT_OUT,
        self::TYPE_SECOND_OPT_OUT,
        self::TYPE_OPT_OUT,
        self::TYPE_CREATE,
        self::TYPE_UPDATE,
        self::TYPE_DELETE
    ];

    const CMS_SCOPE_BACKEND  = 'BE';
    const CMS_SCOPE_FRONTEND = 'FE';

    // tl_settings only
    const CMS_SCOPE_BOTH = 'both';

    const CMS_SCOPES = [
        self::CMS_SCOPE_BACKEND,
        self::CMS_SCOPE_FRONTEND
    ];

    public function getPersonalFieldsAsOptions($includeAdditionalFields = true)
    {
        $fields = General::getFields(
            'tl_privacy_protocol_entry',
            false,
            null,
            [
                'personalField' => true
            ]
        );

        if ($includeAdditionalFields)
        {
            $fields += General::getFields(
                'tl_privacy_protocol_entry',
                true,
                null,
                [
                    'additionalField' => true
                ]
            );
        }

        return $fields;
    }

    public function getCodeFieldsAsOptions()
    {
        return General::getFields(
            'tl_privacy_protocol_entry',
            true,
            null,
            [
                'codeField' => true
            ]
        );
    }

    public function getFieldsAsOptions()
    {
        return General::getFields('tl_privacy_protocol_entry');
    }

    public function listChildren($row)
    {
        $title = $row['id'];

        if (($protocolEntry = \HeimrichHannot\Privacy\Model\ProtocolEntryModel::findByPk($row['id'])) !== null
            && ($protocolArchive = $protocolEntry->getRelated('pid')) !== null)
        {
            $dca              = &$GLOBALS['TL_DCA']['tl_privacy_protocol_entry'];
            $dc               = new DC_HastePlus('tl_privacy_protocol_entry');
            $dc->id           = $protocolEntry->id;
            $dc->activeRecord = $protocolEntry;

            $title = preg_replace_callback(
                '@%([^%]+)%@i',
                function ($arrMatches) use ($protocolEntry, $dca, $dc) {
                    return FormSubmission::prepareSpecialValueForPrint(
                        $protocolEntry->{$arrMatches[1]},
                        $dca['fields'][$arrMatches[1]],
                        'tl_submission',
                        $dc
                    );
                },
                $protocolArchive->titlePattern
            );
        }

        return '<div class="tl_content_left">' . $title . ' <span style="color:#b3b3b3; padding-left:3px">[' . \Date::parse(
                \Config::get('datimFormat'),
                trim($row['dateAdded'])
            ) . ']</span></div>';
    }

    public function modifyDca(DataContainer $dc)
    {
        Controller::loadDataContainer('tl_privacy_protocol_entry');
        $dca = &$GLOBALS['TL_DCA']['tl_privacy_protocol_entry'];

        if (TL_MODE == 'BE')
        {
            // fields
            if (($protocolEntry = ProtocolEntryModel::findByPk($dc->id)) === null)
            {
                return false;
            }

            if (($protocolArchive = ProtocolArchiveModel::findByPk($protocolEntry->pid)) === null)
            {
                return false;
            }

            $allowedPersonalFields = deserialize($protocolArchive->personalFields, true);
            $allowedCodeFields     = deserialize($protocolArchive->codeFields, true);

            foreach ($dca['fields'] as $field => $fieldData)
            {
                $isPersonalField = isset($fieldData['eval']['personalField']) && $fieldData['eval']['personalField'];
                $isCodeField     = isset($fieldData['eval']['codeField']) && $fieldData['eval']['codeField'];

                if ($isPersonalField)
                {
                    $class = $dca['fields'][$field]['eval']['tl_class'] . ' personal-data';

                    $dca['fields'][$field]['eval']['tl_class'] = $class;
                }

                if (!in_array($field, $allowedPersonalFields) && $isPersonalField)
                {
                    unset($dca['fields'][$field]);
                }

                if ((!in_array($field, $allowedCodeFields) || !$protocolArchive->addCodeProtocol) && $isCodeField)
                {
                    unset($dca['fields'][$field]);
                }
            }
        }
    }

    public static function addPersonalPrivacyProtocolFieldsToDca($table)
    {
        if (!Database::getInstance()->tableExists('tl_privacy_protocol_entry'))
        {
            return;
        }

        Controller::loadDataContainer('tl_privacy_protocol_entry');
        System::loadLanguageFile('tl_privacy_protocol_entry');

        Controller::loadDataContainer($table);
        System::loadLanguageFile($table);

        $dca = &$GLOBALS['TL_DCA'][$table];

        foreach ($GLOBALS['TL_DCA']['tl_privacy_protocol_entry']['fields'] as $field => $data)
        {
            if (!isset($data['eval']['personalField']) || !$data['eval']['personalField'])
            {
                continue;
            }

            $dca['fields'][$field] = $data;
        }
    }

    public static function getPersonalFieldsPalette()
    {
        if (!Database::getInstance()->tableExists('tl_privacy_protocol_entry'))
        {
            return '';
        }

        Controller::loadDataContainer('tl_privacy_protocol_entry');
        System::loadLanguageFile('tl_privacy_protocol_entry');

        $paletteFields = [];

        foreach ($GLOBALS['TL_DCA']['tl_privacy_protocol_entry']['fields'] as $field => $data)
        {
            if (!isset($data['eval']['personalField']) || !$data['eval']['personalField'])
            {
                continue;
            }

            $paletteFields[] = $field;
        }

        return implode(',', $paletteFields);
    }

    public function checkPermission()
    {
        $user     = \Contao\BackendUser::getInstance();
        $database = \Contao\Database::getInstance();

        if ($user->isAdmin)
        {
            return;
        }

        // Set the root IDs
        if (!is_array($user->privacy_protocols) || empty($user->privacy_protocols))
        {
            $root = [0];
        }
        else
        {
            $root = $user->privacy_protocols;
        }

        $id = strlen(\Contao\Input::get('id')) ? \Contao\Input::get('id') : CURRENT_ID;

        // Check current action
        switch (\Contao\Input::get('act'))
        {
            case 'paste':
                // Allow
                break;

            case 'create':
                if (!strlen(\Contao\Input::get('pid')) || !in_array(\Contao\Input::get('pid'), $root))
                {
                    throw new \Exception(
                        'Not enough permissions to create privacy_protocol_entry items in privacy_protocol_entry archive ID ' . \Contao\Input::get(
                            'pid'
                        ) . '.'
                    );
                }
                break;

            case 'cut':
            case 'copy':
                if (!in_array(\Contao\Input::get('pid'), $root))
                {
                    throw new \Exception(
                        'Not enough permissions to ' . \Contao\Input::get('act') . ' privacy_protocol_entry item ID ' . $id
                        . ' to privacy_protocol_entry archive ID ' . \Contao\Input::get('pid') . '.'
                    );
                }
            // NO BREAK STATEMENT HERE

            case 'edit':
            case 'show':
            case 'delete':
            case 'feature':
                $objArchive = $database->prepare("SELECT pid FROM tl_privacy_protocol_entry WHERE id=?")->limit(1)->execute($id);

                if ($objArchive->numRows < 1)
                {
                    throw new \Exception('Invalid privacy_protocol_entry item ID ' . $id . '.');
                }

                if (!in_array($objArchive->pid, $root))
                {
                    throw new \Exception(
                        'Not enough permissions to ' . \Contao\Input::get('act') . ' privacy_protocol_entry item ID ' . $id
                        . ' of privacy_protocol_entry archive ID ' . $objArchive->pid . '.'
                    );
                }
                break;

             case 'select':
                if (!in_array($id, $root)) {
                    throw new \Exception('Not enough permissions to access privacy_protocol_entry archive ID '.$id.'.');
                }
                break;

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!in_array($id, $root)) {
                    throw new \Exception('Not enough permissions to access privacy_protocol_entry archive ID '.$id.'.');
                }

                $objArchive = $database->prepare("SELECT id FROM tl_privacy_protocol_entry WHERE pid=?")->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new \Exception('Invalid privacy_protocol_entry archive ID '.$id.'.');
                }

                $session = \Contao\Session::getInstance();

                $sessionData                   = $session->getData();
                $sessionData['CURRENT']['IDS'] = array_intersect((is_array($sessionData['CURRENT']['IDS']) ? $sessionData['CURRENT']['IDS'] : []), $objArchive->fetchEach('id'));
                try {
                    $session->setData($sessionData);
                } catch (\Exception $e) {
                }

                break;

            default:
                if (strlen(\Contao\Input::get('act')))
                {
                    throw new \Exception('Invalid command "' . \Contao\Input::get('act') . '".');
                }
                elseif (!in_array($id, $root))
                {
                    throw new \Exception('Not enough permissions to access privacy_protocol_entry archive ID ' . $id . '.');
                }
                break;
        }
    }
}
