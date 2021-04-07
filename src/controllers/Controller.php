<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\controllers;

use Cacko\Yii2\Widgets\MonacoEditor\models\SettingsInterface;
use yii\helpers\Json;
use yii\web\Response;

class Controller extends \yii\web\Controller implements ControllerInterface
{

    public $enableCsrfValidation = false;

    protected SettingsInterface $userSettings;

    public function __construct($id, $module, SettingsInterface $userSettings, $config = [])
    {
        $this->userSettings = $userSettings;
        parent::__construct($id, $module, $config);
    }


    public function actionSave()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $settings = Json::decode(\Yii::$app->request->rawBody);
        foreach ($settings as $key => $value) {
            switch ($key) {
                case 'theme':
                    $this->userSettings->setTheme($value);
                    break;

                case 'editorHeight':
                    $this->userSettings->setEditorHeight((int) $value);
                    break;

                case 'diffViewrHeight':
                    $this->userSettings->setDiffViewerHeight((int) $value);
                    break;

                case 'renderSideBySide':
                    $this->userSettings->setRenderSideBySide((bool) $value);
                    break;
            }
        }

        $this->userSettings->save();

        return true;
    }
}
