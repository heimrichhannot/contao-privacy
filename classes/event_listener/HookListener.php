<?php

namespace HeimrichHannot\Privacy\EventListener;

use Contao\Config;
use Contao\DataContainer;
use Contao\Model;
use Firebase\JWT\JWT;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Privacy\Backend\ProtocolEntry;
use HeimrichHannot\Privacy\Manager\ProtocolManager;
use HeimrichHannot\Privacy\Privacy;

class HookListener
{
    protected static $callbacks;
    protected static $setCallbacks = [];

    public function initProtocolCallbacks($table)
    {
        if (static::$callbacks === null) {
            static::$callbacks = deserialize(Config::get('privacyProtocolCallbacks'), true);

            foreach (static::$callbacks as $callback) {
                static::$setCallbacks[] = $callback['table'];
            }
        }

        $callbacks = static::$callbacks;

        if (!in_array($table, static::$setCallbacks)) {
            return;
        }

        foreach ($callbacks as $callback) {
            if ($table !== $callback['table']) {
                continue;
            }

            $dca = &$GLOBALS['TL_DCA'][$callback['table']];

            if (!isset($dca['config'][$callback['callback']])) {
                $dca['config'][$callback['callback']] = [];
            }

            $createEntryFunc = function ($data) use ($callback) {
                // restrict to scope
                if ($callback['cmsScope'] === ProtocolEntry::CMS_SCOPE_BOTH || $callback['cmsScope'] === TL_MODE) {
                    $protocolManager = new ProtocolManager();
                    $protocolManager->addEntry($callback['type'], $callback['archive'], $data);
                }
            };

            switch ($callback['callback']) {
                case 'oncreate_callback':
                    $dca['config'][$callback['callback']]['addPrivacyProtocolEntry'] =
                        function ($table, $id, $data, DataContainer $dc) use ($callback, $createEntryFunc) {
                            $instance = $dc->activeRecord ?: General::getModelInstance($callback['table'], $id);

                            $entryData = $instance->row();
                            $entryData['table'] = $callback['table'];

                            $createEntryFunc($entryData);
                        };
                    break;
                case 'onversion_callback':
                    $dca['config'][$callback['callback']]['addPrivacyProtocolEntry'] =
                        function ($table, $id, DataContainer $dc) use ($callback, $createEntryFunc) {
                            $instance = $dc->activeRecord ?: General::getModelInstance($callback['table'], $id);

                            $entryData = $instance->row();
                            $entryData['table'] = $callback['table'];

                            $createEntryFunc($entryData);
                        };
                    break;
                case 'ondelete_callback':
                    $dca['config'][$callback['callback']]['addPrivacyProtocolEntry'] =
                        function (DataContainer $dc, $id) use ($callback, $createEntryFunc) {
                            $instance = $dc->activeRecord ?: General::getModelInstance($callback['table'], $id);

                            $entryData = $instance->row();
                            $entryData['table'] = $callback['table'];

                            $createEntryFunc($entryData);
                        };
                    break;
            }
        }
    }

    public function addInsertTags($tag)
    {
        $tagArray = explode('::', $tag);

        switch ($tagArray[0]) {
            case 'privacy_opt_in_url':
                $dataString = $tagArray[1];
                $data = [];

                foreach (explode('#', $dataString) as $fieldValuePairString)
                {
                    $fieldValuePair = explode(':', $fieldValuePairString);

                    if (count($fieldValuePair) !== 2)
                    {
                        continue;
                    }

                    $data[$fieldValuePair[0]] = $fieldValuePair[1];
                }

                $jumpTo              = isset($tagArray[2]) ? $tagArray[2] : null;
                $url                 = Url::getJumpToPageUrl($jumpTo, true);

                $token = [
                    'data' => $data,
                ];

                if (isset($tagArray[3]) && $tagArray[3])
                {
                    $token['referenceCondition'] = $tagArray[3];
                }

                $jwt = JWT::encode($token, Config::get('encryptionKey'));

                return Url::addQueryString(Privacy::OPT_IN_OUT_ACTION_PARAM . '=optin&' . Privacy::OPT_IN_OUT_TOKEN_PARAM . '=' . $jwt, $url);
                break;
            case 'privacy_opt_out_url':
                $dataString = $tagArray[1];
                $data = [];

                foreach (explode('#', $dataString) as $fieldValuePairString)
                {
                    $fieldValuePair = explode(':', $fieldValuePairString);

                    if (count($fieldValuePair) !== 2)
                    {
                        continue;
                    }

                    $data[$fieldValuePair[0]] = $fieldValuePair[1];
                }

                $jumpTo              = isset($tagArray[2]) ? $tagArray[2] : null;
                $url                 = Url::getJumpToPageUrl($jumpTo, true);

                $token = [
                    'data' => $data,
                ];

                if (isset($tagArray[3]) && $tagArray[3])
                {
                    $token['referenceCondition'] = $tagArray[3];
                }

                if (isset($tagArray[4]) && $tagArray[4])
                {
                    $token['deleteInstance'] = true;
                }

                $jwt = JWT::encode($token, Config::get('encryptionKey'));

                return Url::addQueryString(Privacy::OPT_IN_OUT_ACTION_PARAM . '=optout&' . Privacy::OPT_IN_OUT_TOKEN_PARAM . '=' . $jwt, $url);
                break;
        }

        return false;
    }
}