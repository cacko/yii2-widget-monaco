<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\Widget;

use Cacko\Yii2\Widgets\MonacoEditor\EditorAsset;
use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use ReflectionException;
use yii\helpers\Html;

class Editor extends AbstractEditor
{
    /**
     * @throws \ReflectionException
     */
    protected function registerWidget(): void
    {
        EditorAsset::registerWidget($this, $this->config);
    }

    protected function getHeight(): string
    {
        $userSettings = $this->userSettings;
        $userHeight = $userSettings->getEditorHeight();
        return !empty($userHeight) ? $userHeight : ($this->height ?: 300);
    }

    protected function renderContent(): void
    {
        $defaults = $this->defaults;
        if ($this->showIcon) {
            echo Html::tag(
                'i',
                '',
                [
                    'class' => [
                        $defaults->getThemeIcon(),
                        'icon-editor-contrast',
                        $this->getTheme() === MonacoEditorAsset::THEME_DARK ? 'on-dark' : ''
                    ]
                ]
            );
        }
        if ($this->model) {
            echo Html::activeHiddenInput($this->model, $this->attribute);
        } else {
            echo Html::hiddenInput($this->name);
        }
    }
}
