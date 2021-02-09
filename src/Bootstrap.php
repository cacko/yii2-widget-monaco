<?php

namespace Cacko\Yii2\Widgets\MonacoEditor;

use Cacko\Yii2\Widgets\MonacoEditor\controllers\Controller;
use Cacko\Yii2\Widgets\MonacoEditor\controllers\ControllerInterface;
use Cacko\Yii2\Widgets\MonacoEditor\models\Settings;
use Cacko\Yii2\Widgets\MonacoEditor\models\SettingsInterface;
use yii\base\BootstrapInterface;
use yii\web\AssetConverter;

class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        \Yii::$container->set(AssetConverter::class, ['commands' => ['scss' => ['css', 'pscss --sourcemap  {from} > {to}']]]);

        if (!\Yii::$container->has(SettingsInterface::class)) {
            \Yii::$container->set(SettingsInterface::class, Settings::class);
        }

        if (!\Yii::$container->has(ControllerInterface::class)) {
            \Yii::$container->set(ControllerInterface::class, Controller::class);
        }

        if ($app instanceof \yii\web\Application) {
            $urlManager = $app->getUrlManager();
            $urlManager->enablePrettyUrl = true;
            $app->controllerMap[ControllerInterface::CONTROLLER_ID] = ['class' => ControllerInterface::class];
        }
    }
}
