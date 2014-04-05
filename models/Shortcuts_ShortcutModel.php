<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class Shortcuts_ShortcutModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'id'   => AttributeType::Number,
			'name' => AttributeType::Name,
			'uri' => AttributeType::String,
			'groupId' => AttributeType::Number,
		);
	}

	public function getGroup()
	{
		return craft()->shortcuts->getGroupById($this->groupId);
	}

	public function render()
	{
		return craft()->templates->render('Shortcuts/cp/link', array(
			'link' => $this
		));
	}

	public function __toString()
	{
		return $this->name;
	}
}
