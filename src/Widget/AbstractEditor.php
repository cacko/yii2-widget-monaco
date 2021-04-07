<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\Widget;

use Cacko\Yii2\Widgets\MonacoEditor\controllers\ControllerInterface;
use Cacko\Yii2\Widgets\MonacoEditor\models\EditorOptions;
use Cacko\Yii2\Widgets\MonacoEditor\models\MonacoOptions;
use Cacko\Yii2\Widgets\MonacoEditor\models\SettingsInterface;
use Cacko\Yii2\Widgets\MonacoEditor\MonacoEditorAsset;
use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 *
 * @property-read mixed $controllerId
 * @property-read EditorOptions $config
 * @property-read null|string $theme
 * @property-read string $css
 * @property-read MonacoOptions $editorConfig
 */
abstract class AbstractEditor extends InputWidget
{

    public string $language = 'html';

    public bool $lineNumbers = true;

    public string $wordWrap = 'on';

    public bool $resizable = true;

    public int $height = 0;

    public int $width = 0;

    public bool $useFullHeight = false;

    public $userSettingsUrl;

    public string $theme = '';

    public bool $showIcon = true;

    protected EditorOptions $config;

        /** @var array
     * https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.ieditorconstructionoptions.html
     */
    public $editorOptions = [];

    protected $editorDomId;

    protected SettingsInterface $userSettings;

    protected EditorOptions $defaults;

    public function __construct(SettingsInterface $userSettings, EditorOptions $defaults, $config = [])
    {
        $this->userSettings = $userSettings;
        $this->userSettingsUrl = Url::to([ControllerInterface::ACTIION_SAVE]);
        $this->defaults = $defaults;
        parent::__construct($config);
    }


    abstract protected function registerWidget(): void;

    protected function getEditorConfig(): MonacoOptions
    {
        return Yii::createObject([
            'class' => MonacoOptions::class,
            'lineNumbers' => $this->lineNumbers,
            'language' => in_array($this->language, MonacoEditorAsset::SUPPORTED_LANGUAGES) ? $this->language : MonacoEditorAsset::DEFAULT_LANGUAGE,
            'wordWrap' => $this->wordWrap,
            'scrollBeyondLastLine' => false,
            'theme' => $this->getTheme(),
        ]);
    }

    protected function getTheme(): string
    {
        $userTheme = $this->userSettings->getTheme();
        return in_array($userTheme, array_keys(MonacoEditorAsset::getAvailableThemes()))
            ? $userTheme : ($this->theme ?: MonacoEditorAsset::THEME_DEFAULT);
    }

        /**
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->editorDomId = 'monaco-editor-' . $this->id;
        $this->config = Yii::createObject([
            'class' => EditorOptions::class,
            'editorConfig' => array_merge($this->editorConfig->toArray(), $this->editorOptions),
            'editorId' => $this->editorDomId,
            'inputSelector' => $this->model ? '#' . Html::getInputId($this->model, $this->attribute) : "[name='$this->name']",
            'userSettingsUrl' => $this->userSettingsUrl,
            'resizable' => $this->resizable,
            'height' => $this->getHeight(),
            'useFullHeight' => $this->useFullHeight,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function run(): void
    {
        $defaults = $this->defaults;
        echo Html::beginTag('div', ['id' => $this->id, 'class' => [static::getTargetClass(), $defaults->getBroadcastClass()]]);
        $this->renderContent();
        echo Html::tag('div', '', ['id' => $this->editorDomId, 'class' => 'editor-dom']);
        echo Html::endTag('div');
        $this->registerWidget();
    }

    /**
     * @throws ReflectionException
     */
    public static function getTargetClass(): string
    {
        return Inflector::camel2id(str_replace('\\', '', (new \ReflectionClass(get_called_class()))->getName()));
    }
}
