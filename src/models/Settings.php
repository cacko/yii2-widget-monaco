<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\models;

use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use yii\base\Model;

class Settings extends Model implements SettingsInterface
{
    public string $theme = MonacoEditorAsset::THEME_DARK;

    public int $height = 0;

    public bool $renderSideBySide = false;

    const COOKIES = [
        'theme' => 'editor-settings-theme',
        'height' => 'editor-settings-height',
        'renderSideBySide' => 'editor-settings-renderSideBySide',
    ];

    public function getRenderSideBySide(): bool
    {
        return $this->renderSideBySide;
    }

    public function setRenderSideBySide(bool $value): SettingsInterface
    {
        $this->renderSideBySide = $value;
        return $this;
    }

    public function init()
    {
        parent::init();

        foreach (static::COOKIES as $attr => $target) {
            if (isset($_COOKIE[$target])) {
                $this->{$attr} = unserialize($_COOKIE[$target]);
            }
        }
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setTheme(string $value): SettingsInterface
    {
        $this->theme = $value;
        return $this;
    }

    public function setHeight(int $value): SettingsInterface
    {
        $this->height = $value;
        return $this;
    }

    public function save(): SettingsInterface
    {

        foreach (static::COOKIES as $attr => $target) {
            setcookie(
                $target,
                serialize($this->{$attr}),
                [
                    'expires' => time() + 60 * 60 * 24 * 30,
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'None'
                ]
            );
        }
        return $this;
    }
}
