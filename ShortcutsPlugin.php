<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class ShortcutsPlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Shortcuts');
    }

    public function getVersion()
    {
        return '0.9.12';
    }

    public function getDeveloper()
    {
        return 'Mario Friz';
    }

    public function getDeveloperUrl()
    {
        return 'http://builtbysplash.com';
    }

    public function hasCpSection()
    {
        return true;
    }

    public function init()
    {
        if (craft()->request->isCpRequest())
        {
            // Include required styles and javascript
            craft()->templates->includeCssResource('shortcuts/css/shortcuts.css');
            craft()->templates->includeJsResource('shortcuts/js/shortcuts.js');

            // Get from cache if possible
            $html = craft()->shortcuts->getFromCache();
            if ($html !== false)
            {
                // Render CachedShortcutsBar
                $js = 'var bar = new CachedShortcutsBar(\''.$html.'\'); '
                . 'bar.render();';
            }
            else
            {
                // Get all groups with all shortcuts
                $groups = craft()->shortcuts->getGroupsWithShortcuts();

                $shortcuts = array();

                // Format array to be easily readable in json
                foreach ($groups as $group) {
                    if ($group->id == 1)
                    {
                        foreach ($group->shortcuts as $shortcut) {
                            $shortcuts[$shortcut->name] = $shortcut->uri;
                        }
                    }
                    else
                    {
                        if (count($group->shortcuts) > 0)
                        {
                            $shortcuts[$group->name] = array();
                        }
                        foreach ($group->shortcuts as $shortcut) {
                            $shortcuts[$group->name][$shortcut->name] = $shortcut->uri;
                        }
                    }
                }

                // Get sections for entry button
                $sections = craft()->sections->getEditableSections();

                $newEntrySections = array();
                if (count($sections) > 0)
                {
                    if (Craft::hasPackage('PublishPro') == false)
                    {
                        $sections = array($sections[0]);
                    }
                }

                // Render ShortcutsBar
                $js = 'var bar = new ShortcutsBar(\''
                    . JsonHelper::encode($shortcuts) . '\', \''
                    . JsonHelper::encode($sections) . '\'); '
                . 'bar.render();';
            }

            // Trigger click if cut key is set for ajax powered pages
            if (strpos(craft()->request->getQueryStringWithoutPath(), 'cut') !== false)
            {
                $cutKey = craft()->request->getRequiredQuery('cut');
                $js = '$(document).ready(function(){
                    $(\'nav li a[data-key="' . $cutKey . '"]\').trigger(\'click\');
                })';
            }
            craft()->templates->includeJs($js);
        }
    }

    public function onAfterInstall()
    {
        $defaultGroup = new Shortcuts_GroupRecord();
        $defaultGroup->name = "Default";
        $defaultGroup->save();

        $defaultShortcuts = array(
            'Fields' => 'settings/fields',
            'Sections' => 'settings/sections',
            'Assets' => 'settings/assets',
            'Globals' => 'settings/globals',
            'Plugins' => 'settings/plugins',
        );

        foreach ($defaultShortcuts as $name => $uri) {
            $shortcut = new Shortcuts_ShortcutRecord();
            $shortcut->name = $name;
            $shortcut->uri = $uri;
            $shortcut->groupId = $defaultGroup->id;
            $shortcut->save();
        }
    }

    public function hookRegisterCpRoutes()
    {
        return array(
            'shortcuts\/(?P<groupId>\d+)' => 'shortcuts/index',
            'shortcuts\/edit\/(?P<shortcutId>\d+)' => 'shortcuts/_edit',
            'shortcuts\/new' => 'shortcuts/_edit',
        );
    }
}
