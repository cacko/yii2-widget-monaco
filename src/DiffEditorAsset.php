<?php

namespace Cacko\Yii2\Widgets\MonacoEditor;

use Cacko\Yii2\Widgets\MonacoEditor\models\DiffViewerOptions;

class DiffEditorAsset extends EditorAsset
{

    const PLUGIN_NAME = 'monacoDiffEditor';

    public $js = [
        'js/jquery-monaco-diffeditor.js',
    ];
}
