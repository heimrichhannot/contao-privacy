<?php

namespace HeimrichHannot\Privacy\Backend;

class ProtocolArchive extends \Contao\Backend
{
    public function checkPermission()
    {
        $user     = \Contao\BackendUser::getInstance();
        $database = \Contao\Database::getInstance();

        if ($user->isAdmin)
        {
            return;
        }

        // Set root IDs
        if (!is_array($user->privacy_protocols) || empty($user->privacy_protocols))
        {
            $root = [0];
        }
        else
        {
            $root = $user->privacy_protocols;
        }

        $GLOBALS['TL_DCA']['tl_privacy_protocol_archive']['list']['sorting']['root'] = $root;

        // Check permissions to add archives
        if (!$user->hasAccess('create', 'privacy_protocolp'))
        {
            $GLOBALS['TL_DCA']['tl_privacy_protocol_archive']['config']['closed'] = true;
        }

        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
        $objSession = \Contao\Session::getInstance();

        // Check current action
        switch (\Contao\Input::get('act'))
        {
            case 'create':
            case 'select':
                // Allow
                break;

            case 'edit':
                // Dynamically add the record to the user profile
                if (!in_array(\Contao\Input::get('id'), $root))
                {
                    /** @var \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $sessionBag */
                    $sessionBag = $objSession->getBag('contao_backend');

                    $arrNew = $sessionBag->get('new_records');

                    if (is_array($arrNew['tl_privacy_protocol_archive']) && in_array(\Contao\Input::get('id'), $arrNew['tl_privacy_protocol_archive']))
                    {
                        // Add the permissions on group level
                        if ($user->inherit != 'custom')
                        {
                            $objGroup = $database->execute("SELECT id, privacy_protocols, privacy_protocolp FROM tl_user_group WHERE id IN(" . implode(',', array_map('intval', $user->groups)) . ")");

                            while ($objGroup->next())
                            {
                                $arrModulep = deserialize($objGroup->privacy_protocolp);

                                if (is_array($arrModulep) && in_array('create', $arrModulep))
                                {
                                    $arrModules = deserialize($objGroup->privacy_protocols, true);
                                    $arrModules[] = \Contao\Input::get('id');

                                    $database->prepare("UPDATE tl_user_group SET privacy_protocols=? WHERE id=?")->execute(serialize($arrModules), $objGroup->id);
                                }
                            }
                        }

                        // Add the permissions on user level
                        if ($user->inherit != 'group')
                        {
                            $user = $database->prepare("SELECT privacy_protocols, privacy_protocolp FROM tl_user WHERE id=?")
                                ->limit(1)
                                ->execute($user->id);

                            $arrModulep = deserialize($user->privacy_protocolp);

                            if (is_array($arrModulep) && in_array('create', $arrModulep))
                            {
                                $arrModules = deserialize($user->privacy_protocols, true);
                                $arrModules[] = \Contao\Input::get('id');

                                $database->prepare("UPDATE tl_user SET privacy_protocols=? WHERE id=?")
                                    ->execute(serialize($arrModules), $user->id);
                            }
                        }

                        // Add the new element to the user object
                        $root[] = \Contao\Input::get('id');
                        $user->privacy_protocols = $root;
                    }
                }
            // No break;

            case 'copy':
            case 'delete':
            case 'show':
                if (!in_array(\Contao\Input::get('id'), $root) || (\Contao\Input::get('act') == 'delete' && !$user->hasAccess('delete', 'privacy_protocolp')))
                {
                    throw new \Exception('Not enough permissions to ' . \Contao\Input::get('act') . ' privacy_protocol_archive ID ' . \Contao\Input::get('id') . '.');
                }
                break;

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
                $session = $objSession->all();
                if (\Contao\Input::get('act') == 'deleteAll' && !$user->hasAccess('delete', 'privacy_protocolp'))
                {
                    $session['CURRENT']['IDS'] = [];
                }
                else
                {
                    $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
                }
                $objSession->replace($session);
                break;

            default:
                if (strlen(\Contao\Input::get('act')))
                {
                    throw new \Exception('Not enough permissions to ' . \Contao\Input::get('act') . ' privacy_protocol_archives.');
                }
                break;
        }
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return \Contao\BackendUser::getInstance()->canEditFieldsOf('tl_privacy_protocol_archive') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    public function copyArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return \Contao\BackendUser::getInstance()->hasAccess('create', 'privacy_protocolp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return \Contao\BackendUser::getInstance()->hasAccess('delete', 'privacy_protocolp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }
}