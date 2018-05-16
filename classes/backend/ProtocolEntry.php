<?php

namespace HeimrichHannot\Privacy\Backend;

use HeimrichHannot\Haste\Dca\General;

class ProtocolEntry extends \Contao\Backend
{
    public function getFieldsAsOptions()
    {
        return General::getFields('tl_privacy_protocol_entry', true);
    }

    public function listChildren($arrRow)
    {
        return '<div class="tl_content_left">' . ($arrRow['title'] ?: $arrRow['id']) . ' <span style="color:#b3b3b3; padding-left:3px">[' .
               \Date::parse(\Contao\Config::get('datimFormat'), trim($arrRow['dateAdded'])) . ']</span></div>';
    }

    public function checkPermission()
    {
        $user = \Contao\BackendUser::getInstance();
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
                    throw new \Exception('Not enough permissions to create privacy_protocol_entry items in privacy_protocol_entry archive ID ' . \Contao\Input::get('pid') . '.');
                }
                break;

            case 'cut':
            case 'copy':
                if (!in_array(\Contao\Input::get('pid'), $root))
                {
                    throw new \Exception('Not enough permissions to ' . \Contao\Input::get('act') . ' privacy_protocol_entry item ID ' . $id . ' to privacy_protocol_entry archive ID ' . \Contao\Input::get('pid') . '.');
                }
            // NO BREAK STATEMENT HERE

            case 'edit':
            case 'show':
            case 'delete':
            case 'toggle':
            case 'feature':
                $objArchive = $database->prepare("SELECT pid FROM tl_privacy_protocol_entry WHERE id=?")
                    ->limit(1)
                    ->execute($id);

                if ($objArchive->numRows < 1)
                {
                    throw new \Exception('Invalid privacy_protocol_entry item ID ' . $id . '.');
                }

                if (!in_array($objArchive->pid, $root))
                {
                    throw new \Exception('Not enough permissions to ' . \Contao\Input::get('act') . ' privacy_protocol_entry item ID ' . $id . ' of privacy_protocol_entry archive ID ' . $objArchive->pid . '.');
                }
                break;

            case 'select':
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!in_array($id, $root))
                {
                    throw new \Exception('Not enough permissions to access privacy_protocol_entry archive ID ' . $id . '.');
                }

                $objArchive = $database->prepare("SELECT id FROM tl_privacy_protocol_entry WHERE pid=?")
                    ->execute($id);

                if ($objArchive->numRows < 1)
                {
                    throw new \Exception('Invalid privacy_protocol_entry archive ID ' . $id . '.');
                }

                /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
                $session = \Contao\Session::getInstance();

                $session = $session->all();
                $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
                $session->replace($session);
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