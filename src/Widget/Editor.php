<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\Widget;

use Cacko\Yii2\Widgets\MonacoEditor\controllers\ControllerInterface;
use Cacko\Yii2\Widgets\MonacoEditor\EditorAsset;
use Cacko\Yii2\Widgets\MonacoEditor\models\SettingsInterface;
use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use ReflectionException;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 *
 * @property-read mixed $controllerId
 * @property-read array $config
 * @property-read null|string $theme
 * @property-read string $css
 * @property-read array $editorConfig
 */
class Editor extends InputWidget
{

    public string $language = 'html';

    public bool $lineNumbers = true;

    public string $wordWrap = 'on';

    public bool $resizable = true;

    public int $height = 0;

    public int $width = 0;

    public bool $useFullHeight = false;

    public bool $readOnly = false;

    public $userSettingsUrl;

    public string $theme = '';

    public bool $showIcon = true;

    /** @var array
     * https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.ieditorconstructionoptions.html
     */
    public $editorOptions = [];

    protected $editorDomId;

    protected $config = [];

    protected SettingsInterface $userSettings;

    public function __construct(SettingsInterface $userSettings, $config = [])
    {
        $this->userSettings = $userSettings;
        $this->userSettingsUrl = Url::to([ControllerInterface::ACTIION_SAVE]);
        parent::__construct($config);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->editorDomId = 'monaco-editor-' . $this->id;
        $this->config = [
            'editorConfig' => array_merge($this->editorConfig, (array) $this->editorOptions),
            'editorId' => $this->editorDomId,
            'inputSelector' => $this->model ? '#' . Html::getInputId($this->model, $this->attribute) : "[name='$this->name']",
            'userSettingsUrl' => $this->userSettingsUrl,
            'resizable' => $this->resizable,
            'height' => $this->getHeight(),
            'width' => $this->width,
            'readOnly' => $this->readOnly,
            'useFullHeight' => $this->useFullHeight,
            'themes' => [
                'light' => MonacoEditorAsset::THEME_DEFAULT,
                'dark' => MonacoEditorAsset::THEME_DARK,
            ]
        ];
    }

    /**
     * @throws \ReflectionException
     */
    protected function registerWidget(): void
    {
        EditorAsset::registerWidget($this, $this->config);
    }

    protected function getControllerId(): string
    {
        return Yii::$app->controller->id;
    }

    protected function getHeight(): string
    {
        $userSettings = $this->userSettings;
        $userHeight = $userSettings->getHeight();
        return !empty($userHeight) ? $userHeight : ($this->height ?: 300);
    }

    protected function getTheme(): string
    {
        $userTheme = $this->userSettings->getTheme();
        return in_array($userTheme, array_keys(MonacoEditorAsset::getAvailableThemes()))
            ? $userTheme : ($this->theme ?: MonacoEditorAsset::THEME_DEFAULT);
    }

    protected function getEditorConfig(): array
    {
        return [
            'lineNumbers' => $this->lineNumbers,
            'language' => in_array($this->language, MonacoEditorAsset::SUPPORTED_LANGUAGES) ? $this->language : MonacoEditorAsset::DEFAULT_LANGUAGE,
            'wordWrap' => $this->wordWrap,
            'scrollBeyondLastLine' => false,
            'theme' => $this->getTheme(),
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function run(): void
    {
        echo Html::beginTag('div', ['id' => $this->id, 'class' => [static::getTargetClass(), 'cacko-widget-monaco']]);
        $this->renderContent();
        echo Html::tag('div', '', ['id' => $this->editorDomId, 'class' => 'editor-dom']);
        echo Html::endTag('div');
        $this->registerWidget();
    }

    protected function renderContent(): void
    {
        if ($this->showIcon) {
            echo Html::tag(
                'i',
                '',
                [
                    'class' => [
                        'theme-selector icon-editor-contrast',
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

    /**
     * @throws ReflectionException
     */
    public static function getTargetClass(): string
    {
        return Inflector::camel2id(str_replace('\\', '', (new \ReflectionClass(get_called_class()))->getName()));
    }
}
