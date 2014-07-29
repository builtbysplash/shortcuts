<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class Shortcuts_ShortcutRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'shortcuts_shortcuts';
    }

    protected function defineAttributes()
    {
        return array(
            'name' => array(AttributeType::String, 'required' => true),
            'uri' => array(AttributeType::String, 'required' => true),
        );
    }

    public function defineRelations()
    {
        return array(
            'group' => array(static::BELONGS_TO, 'Shortcuts_GroupRecord', 'onDelete' => static::CASCADE),
        );
    }

    public function scopes()
    {
        return array(
            'ordered' => array('order' => 'name'),
        );
    }
}
