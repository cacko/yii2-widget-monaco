<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\models;

use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use JsonSerializable;
use yii\base\Model;

class EditorOptions extends Model implements JsonSerializable
{
    public string $themeSelector = ".theme-selector";
    public array $editorConfig = [];
    public string $editorId = '';
    public string $inputSelector = '';
    public string $userSettingsUrl = '#';
    public bool $resizable = true;
    public string $minHeight = '5rem';
    public string $height = '100%';
    public string $useFullHeight = '';
    public array $themes = [
        'light' => MonacoEditorAsset::THEME_DEFAULT,
        'dark' => MonacoEditorAsset::THEME_DARK
    ];
    public string $inputLeftSelector = '[name="parent"]';
    public string $inputRightSelector = '[name="current"]';
    public string $layoutSelector = '.layout-selector';
    public string $broadcastSelector = '.cacko-widget-monaco';

    public function jsonSerialize(): mixed
    {
        return array_filter($this->getAttributes(), fn ($val) => $val !== null);
    }

    public function getBroadcastClass()
    {
        return ltrim($this->broadcastSelector, '.');
    }

    public function getThemeIcon()
    {
        return ltrim($this->themeSelector, '.');
    }

    public function getLayoutIcon()
    {
        return ltrim($this->layoutSelector, '.');
    }
}
