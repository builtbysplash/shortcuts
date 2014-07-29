<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class Shortcuts_GroupRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'shortcuts_groups';
    }

    protected function defineAttributes()
    {
        return array(
            'name' => array(AttributeType::String, 'required' => true),
        );
    }

    public function defineRelations()
    {
        return array(
            'shortcuts' => array(static::HAS_MANY, 'Shortcuts_ShortcutRecord', 'groupId'),
        );
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('name'), 'unique' => true),
        );
    }

    public function scopes()
    {
        return array(
            'ordered' => array('order' => 'name'),
        );
    }
}
