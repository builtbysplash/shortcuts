<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class Shortcuts_GroupModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'   => AttributeType::Number,
            'name' => AttributeType::Name,
        );
    }

    public function getShortcuts()
    {
        return craft()->shortcuts->getShortcutsByGroupId($this->id);
    }

    function __toString()
    {
        return $this->name;
    }
}
