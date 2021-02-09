<?php

namespace Cacko\Yii2\Widgets\MonacoEditor;


use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\AssetBundle;
use yii\web\View;

class MonacoEditorAsset extends AssetBundle
{

    const THEME_DEFAULT = 'vs';
    const THEME_DARK = 'vs-dark';

    const SUPPORTED_LANGUAGES = ['html', 'css', 'javascript', 'xml', 'json'];

    const DEFAULT_LANGUAGE = 'html';

    public $sourcePath = '@npm/monaco-editor/min';

    public $js = [
        'vs/loader.js',
    ];

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        $am = $view->getAssetManager();

        $vsPath = 'https://' . $_SERVER['HTTP_HOST'] . $am->getAssetUrl($this, 'vs');
        $basePath = 'https://' . $_SERVER['HTTP_HOST'] . $am->getAssetUrl($this, '');

        $view->registerJs("
            var require = { paths: { 'vs': '{$vsPath}' }};
            window.MonacoEnvironment = { getWorkerUrl: () => proxy };
            
            let proxy = URL.createObjectURL(new Blob([`
                self.MonacoEnvironment = {
                    baseUrl: '{$basePath}'
                };
                importScripts('{$vsPath}/base/worker/workerMain.js');
            `], { type: 'text/javascript' }));
        ", View::POS_BEGIN);

    }

    public static function getAvailableThemes(): array
    {
        return array_reduce(array_values(static::getConstants('THEME_')), function ($result, $theme) {
            $result[$theme] = Inflector::camel2words(Inflector::id2camel($theme));
            return $result;
        }, []);
    }

    protected static function getConstants($prefix): array
    {
        return array_filter((new \ReflectionClass(static::class))->getConstants(), function ($k) use ($prefix) {
            return StringHelper::startsWith($k, $prefix);
        }, ARRAY_FILTER_USE_KEY);
    }

}