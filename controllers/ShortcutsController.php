<?php

/**
 * Shortcuts provides an easy way to add shortcuts to your Craft admin area.
 *
 * @package   Craft Shortcuts
 * @author    Mario Friz
 */

namespace Craft;

class ShortcutsController extends BaseController
{
    public function actionSaveGroup()
    {
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        $group = new Shortcuts_GroupModel();
        $group->id = craft()->request->getPost('id');
        $group->name = craft()->request->getRequiredPost('name');

        $isNewGroup = empty($group->id);

        if (craft()->shortcuts->saveGroup($group))
        {
            if ($isNewGroup)
            {
                craft()->userSession->setNotice(Craft::t('Group added.'));
            }

            $this->returnJson(array(
                'success' => true,
                'group'   => $group->getAttributes(),
            ));
        }
        else
        {
            $this->returnJson(array(
                'errors' => $group->getErrors(),
            ));
        }
    }

    public function actionDeleteGroup()
    {
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        $groupId = craft()->request->getRequiredPost('id');
        $success = craft()->shortcuts->deleteGroupById($groupId);

        craft()->userSession->setNotice(Craft::t('Group deleted.'));

        $this->returnJson(array(
            'success' => $success,
        ));
    }

    public function actionSaveShortcut()
    {
        $this->requirePostRequest();

        $shortcut = new Shortcuts_ShortcutModel();

        $shortcut->id = craft()->request->getPost('shortcutId');
        $shortcut->groupId = craft()->request->getRequiredPost('group');
        $shortcut->name = craft()->request->getPost('name');
        $shortcut->uri = craft()->request->getPost('uri');

        if (craft()->shortcuts->saveShortcut($shortcut))
        {
            craft()->userSession->setNotice(Craft::t('Shortcut saved.'));
            $this->redirectToPostedUrl($shortcut);
        }
        else
        {
            craft()->userSession->setError(Craft::t('Couldn’t save shortcut.'));
        }

        craft()->urlManager->setRouteVariables(array(
            'shortcut' => $shortcut
        ));
    }

    public function actionDeleteShortcut()
    {
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        $shortcutId = craft()->request->getRequiredPost('id');
        $success = craft()->shortcuts->deleteShortcutById($shortcutId);

        craft()->userSession->setNotice(Craft::t('Shortcut deleted.'));

        $this->returnJson(array(
            'success' => $success,
        ));
    }

    public function actionSaveToCache()
    {
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        $html = craft()->request->getRequiredPost('html');

        $success = craft()->shortcuts->saveToCache($html);

        $this->returnJson(array(
            'success' => $success,
        ));
    }
}
