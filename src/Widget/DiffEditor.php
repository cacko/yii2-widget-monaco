<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\Widget;


use Cacko\Yii2\Widgets\MonacoEditor\DiffEditorAsset;
use Cacko\Yii2\Widgets\MonacoEditor\models\MonacoOptions;
use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 *
 * @property-read null|string $renderSideBySide
 */
class DiffEditor extends AbstractEditor
{

    public string $title = '';

    public Model $parent;

    public bool $readOnly = true;

    protected function getRenderSideBySide(): bool
    {
        $userSettings = $this->userSettings;
        return $userSettings->getRenderSideBySide();
    }

    protected function getEditorConfig(): MonacoOptions
    {
        return Yii::createObject(array_merge(
            parent::getEditorConfig()->toArray(),
            [
                'class' => MonacoOptions::class,
                'readOnly' => true,
                'renderSideBySide' => $this->getRenderSideBySide()
            ]
        ));
    }


    protected function getHeight(): string
    {
        $userSettings = $this->userSettings;
        $userHeight = $userSettings->getDiffViewerHeight();
        return !empty($userHeight) ? $userHeight : ($this->height ?: 300);
    }
    /**
     * @throws \ReflectionException
     */
    protected function registerWidget(): void
    {
        DiffEditorAsset::registerWidget($this, $this->config);
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
                        $defaults->getLayoutIcon(),
                        'icon-editor-shuffle',
                        $this->getTheme() === MonacoEditorAsset::THEME_DARK ? 'on-dark' : ''
                    ]
                ]
            );
        }
        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::tag('div', $this->evalValue('title', $this->parent), ['class' => 'col-xs-6']);
        echo Html::tag('div', $this->evalValue('title', $this->model), ['class' => 'col-xs-6 text-right']);
        echo Html::endTag('div');
        echo Html::hiddenInput('current', $this->model->{$this->attribute});
        echo Html::hiddenInput('parent', $this->parent->{$this->attribute});
    }

    protected function evalValue($attribute, $model)
    {
        $value = $this->{$attribute};

        if ($value instanceof \Closure) {
            return $value($model, $this->attribute);
        }

        return $value;
    }
}
