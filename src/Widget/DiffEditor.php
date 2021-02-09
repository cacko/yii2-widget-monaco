<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\Widget;


use Cacko\Yii2\Widgets\MonacoEditor\DiffEditorAsset;
use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use yii\helpers\Html;

/**
 *
 * @property-read null|string $renderSideBySide
 */
class DiffEditor extends Editor
{

    public $title;
    public $parent;
    public $attribute;

    /** @var array
     * https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.idiffeditorconstructionoptions.html
     */
    public $editorOptions = [];

    protected function getRenderSideBySide(): bool
    {
        $userSettings = $this->userSettings;
        return $userSettings->getRenderSideBySide();
    }

    protected function getEditorConfig(): array
    {
        return array_merge(parent::getEditorConfig(), [
            'readOnly' => true,
            'renderSideBySide' => $this->getRenderSideBySide()
        ]);
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
        echo Html::tag('i', '', ['class' => ['layout-selector icon-editor-shuffle', $this->getTheme() === MonacoEditorAsset::THEME_DARK ? 'on-dark' : '']]);
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
