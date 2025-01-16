<?php

namespace samuelreichor\quickedit\controllers;

use Craft;
use craft\web\Controller;
use yii\base\Response;

class DefaultController extends Controller
{
    public function actionCheckPermission(int $entryId): Response
    {
        $canEdit = false;
        $entry = Craft::$app->entries->getEntryById($entryId);

        if ($entry) {
            $canEdit = Craft::$app->getElements()->canSave($entry);
        }

        return $this->asJson(['canEdit' => $canEdit]);
    }
}
