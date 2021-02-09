<?php

namespace Cacko\Yii2\Widgets\MonacoEditor\controllers;


interface ControllerInterface
{

    const CONTROLLER_ID = 'monaco-settngs';

    const ACTIION_SAVE = '/monaco-settngs/save';

    public function actionSave();
}
