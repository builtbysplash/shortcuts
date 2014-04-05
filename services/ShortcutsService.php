<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class ShortcutsService extends BaseApplicationComponent
{
    public function getGroupsWithShortcuts()
    {
        $groupRecords = Shortcuts_GroupRecord::model()->with('shortcuts')->findAll();
        $groups = Shortcuts_GroupModel::populateModels($groupRecords, 'id');

        $groupsWithModels = array();
        foreach ($groups as $group)
        {
            $group->elements = Shortcuts_ShortcutModel::populateModels($group->shortcuts, 'id');
            $groupsWithModels[] = $group;
        }

        return $groupsWithModels;
    }

    public function saveGroup(Shortcuts_GroupModel $group)
    {
        $groupRecord = $this->_getGroupRecord($group);

        $groupRecord->name = $group->name;

        $this->clearCache();

        if ($groupRecord->validate())
        {
            $groupRecord->save(false);

            // Now that we have an ID, save it on the model & models
            if (!$group->id)
            {
                $group->id = $groupRecord->id;
            }

            return true;
        }
        else
        {
            $group->addErrors($groupRecord->getErrors());
            return false;
        }
    }

    public function deleteGroupById($groupId)
    {
        $this->clearCache();

        $affectedRows = craft()->db->createCommand()->delete('shortcuts_groups', array('id' => $groupId));
        return (bool) $affectedRows;
    }

    public function getShortcutById($shortcutId)
    {
        $shortcutRecord = Shortcuts_ShortcutRecord::model()->findById($shortcutId);
        return Shortcuts_ShortcutModel::populateModel($shortcutRecord);
    }

    public function getGroupById($groupId)
    {
        $groupRecord = Shortcuts_GroupRecord::model()->findById($groupId);
        return Shortcuts_GroupModel::populateModel($groupRecord);
    }

    public function getShortcutsByGroupId($groupId = 1)
    {
        $shortcutsRecords = Shortcuts_ShortcutRecord::model()->ordered()->findAllByAttributes(array(
            'groupId' => $groupId,
        ));

        return Shortcuts_ShortcutModel::populateModels($shortcutsRecords);
    }

    public function getAllGroups()
    {
        $groupRecords = Shortcuts_GroupRecord::model()->ordered()->findAll();
        $groups = Shortcuts_GroupModel::populateModels($groupRecords, 'id');

        return $groups;
    }

    public function getAllShortcuts()
    {
        $shortcutsRecords = Shortcuts_ShortcutRecord::model()->ordered()->findAll();
        $shortcuts = Shortcuts_ShortcutModel::populateModels($shortcutsRecords, 'id');

        return $shortcuts;
    }

    public function saveShortcut(Shortcuts_ShortcutModel $shortcut)
    {
        $shortcutRecord = $this->_getShortcutRecord($shortcut);

        $shortcutRecord->name = $shortcut->name;
        $shortcutRecord->groupId = $shortcut->groupId;
        $shortcutRecord->uri = $shortcut->uri;

        $this->clearCache();

        if ($shortcutRecord->validate())
        {
            $shortcutRecord->save(false);

            // Now that we have an ID, save it on the model & models
            if (!$shortcut->id)
            {
                $shortcut->id = $shortcutRecord->id;
            }

            return true;
        }
        else
        {
            $shortcut->addErrors($shortcutRecord->getErrors());
            return false;
        }
    }

    public function deleteShortcutById($shortcutId)
    {
        $this->clearCache();

        $affectedRows = craft()->db->createCommand()->delete('shortcuts_shortcuts', array('id' => $shortcutId));
        return (bool) $affectedRows;
    }

    public function _getGroupRecord(Shortcuts_GroupModel $group)
    {
        if ($group->id)
        {
            $groupRecord = Shortcuts_GroupRecord::model()->findById($group->id);

            if (!$groupRecord)
            {
                throw new Exception(Craft::t('No shortcuts group exists with the ID “{id}”', array('id' => $group->id)));
            }
        }
        else
        {
            $groupRecord = new Shortcuts_GroupRecord();
        }

        return $groupRecord;
    }

    public function _getShortcutRecord(Shortcuts_ShortcutModel $shortcut)
    {
        if ($shortcut->id)
        {
            $shortcutRecord = Shortcuts_ShortcutRecord::model()->findById($shortcut->id);

            if (!$shortcutRecord)
            {
                throw new Exception(Craft::t('No shortcut exists with the ID “{id}”', array('id' => $shortcut->id)));
            }
        }
        else
        {
            $shortcutRecord = new Shortcuts_ShortcutRecord();
        }

        return $shortcutRecord;
    }
}
