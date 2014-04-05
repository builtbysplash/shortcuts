<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class Shortcuts_BarModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'elements' => AttributeType::Mixed,
		);
	}

	public function init($groupsWithShortcuts)
	{
		$defaultGroup = array_shift($groupsWithShortcuts);
		$defaultLinks = array();
		foreach ($defaultGroup->shortcuts as $link)
		{
			$defaultLinks[] = $link;
		}
		$this->elements = array_merge($defaultLinks, $groupsWithShortcuts);
	}

	public function render()
	{
		return craft()->templates->render('Shortcuts/cp/bar', array(
			'bar' => $this
		));
	}
}
