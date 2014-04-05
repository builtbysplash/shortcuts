<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class ShortcutsVariable
{
    public function getAllGroups()
    {
        return craft()->shortcuts->getAllGroups();
    }

    public function getAllShortcuts()
    {
        return craft()->shortcuts->getAllShortcuts();
    }

    public function getShortcutById($shortcutId)
    {
        return craft()->shortcuts->getShortcutById($shortcutId);
    }

    public function getGroupsWithShortcuts()
    {
        return craft()->shortcuts->getGroupsWithShortcuts();
    }
}
