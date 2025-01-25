<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\models;

use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use JsonSerializable;
use yii\base\Model;

class MonacoOptions extends Model implements JsonSerializable
{

    public bool $lineNumbers = true;
    public string $language = 'html';
    public  string $wordWrap = 'on';
    public string $scrollBeyondLastLine = 'false';
    public string $theme = MonacoEditorAsset::THEME_DARK;
    public bool $readOnly = false;
    public bool $renderSideBySide = false;

    public function jsonSerialize(): mixed
    {
        return array_filter($this->getAttributes(), fn ($val) => $val !== null);
    }
}
