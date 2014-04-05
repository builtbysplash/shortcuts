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

			// Get all groups with all shortcuts
			$groupsWithShortcuts = craft()->shortcuts->getGroupsWithShortcuts();

			// Prepare bar for rendering
			$shortcutsBar = new Shortcuts_BarModel();
			$shortcutsBar->init($groupsWithShortcuts);

			// Render shortcuts bar
			$html = $shortcutsBar->render();
			craft()->templates->render('Shortcuts/cp/shortcuts', array(
				'barHtml' => $html
			));
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
			'Tags' => 'settings/tags',
			'Categories' => 'settings/categories',
		);

		foreach ($defaultShortcuts as $name => $uri) {
			$shortcut = new Shortcuts_ShortcutRecord();
			$shortcut->name = $name;
			$shortcut->uri = $uri;
			$shortcut->groupId = $defaultGroup->id;
			$shortcut->save();
		}
	}

	public function registerCpRoutes()
	{
		return array(
			'shortcuts\/(?P<groupId>\d+)' => 'shortcuts/index',
			'shortcuts\/edit\/(?P<shortcutId>\d+)' => 'shortcuts/_edit',
			'shortcuts\/new' => 'shortcuts/_edit',
		);
	}
}
