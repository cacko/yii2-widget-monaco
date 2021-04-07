<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\models;

interface SettingsInterface
{

    public function getTheme(): string;

    public function getEditorHeight(): int;

    public function getDiffViewerHeight(): int;


    public function getRenderSideBySide(): bool;

    public function setTheme(string $value): SettingsInterface;

    public function setEditorHeight(int $value): SettingsInterface;

    public function setDiffViewerHeight(int $value): SettingsInterface;


    public function setRenderSideBySide(bool $value): SettingsInterface;

    public function save(): SettingsInterface;
}
