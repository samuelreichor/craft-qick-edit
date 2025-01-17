<?php

namespace samuelreichor\quickedit;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\View;
use samuelreichor\quickedit\models\Settings;
use samuelreichor\quickedit\services\EditService;
use yii\base\Event;

/**
 * craft-quick-edit plugin
 *
 * @method static QuickEdit getInstance()
 * @method Settings getSettings()
 * @author Samuel Reichör <samuelreichor@gmail.com>
 * @copyright Samuel Reichör
 * @license MIT
 *
 * @property EditService $edit
 */
class QuickEdit extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'edit' => ['class' => EditService::class],
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();

        Craft::$app->onInit(function() {
            $this->edit->renderQuickEdit();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('craft-quick-edit/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['craft-quick-edit'] = __DIR__ . '/templates';
            }
        );

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules = array_merge($event->rules, [
                    'GET /api/quickEdit/getPermission/<entryId>' => 'craft-quick-edit/default/check-permission',
                ]);
            }
        );
    }
}
