# Yii2 Monaco Editor Widget

Simplifies adding [Monaco Editor](https://microsoft.github.io/monaco-editor/) if you are screwed having to use Yii2 with its amazing jQuery inhericance.

## Install
```Shell
composer require cacko/yii2-widget-monaco
```

## Demo
Sorta demo and playground - https://yii.cacko.net/monaco/widget.

## Usage

### Options

* editorConfig - generally you can pass every option [from the API](https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.ieditorconstructionoptions.html) which will override any crap you put before


#### Editor
with ActiveForm:

```Php

use Cacko\Yii2\Widgets\MonacoEditor\Widget\Editor as MonacoEditor;

<?= $form->field($model, 'script')->widget(MonacoEditor::class)->label(t('Code')) ?>
 ```
 as a standalone widget
 
 ```php
 use Cacko\Yii2\Widgets\MonacoEditor\Widget\Editor as MonacoEditor;

 <?= MonacoEditor::widget([
     'model' => $model,
     'attribute' => 'script',
     'language' => 'javascript',
 ]) ?>
 ```
 or without a model
  ```php
 use Cacko\Yii2\Widgets\MonacoEditor\Widget\Editor as MonacoEditor;

 <?= MonacoEditor::widget([
     'name' => 'script',
     'language' => 'javascript',
 ]) ?>
 ```
 
 #### Diff Viewer
```Php

use Cacko\Yii2\Widgets\MonacoEditor\Widget\DiffEditor as MonacoDiffEditor;

<?= MonacoDiffEditor::widget([
    'model' => $model,
    'parent' => $parent,
    'attribute' => $attribute,
]) ?>
 ```
 
 ### Persistance
 
 for persisting the editor/diff options not in a cookie, inject your own implementation of Cacko\Yii2\Widgets\MonacoEditor\models\EditorSettingsInterface.
 
 ```php
\Yii::$container->set(EditorSettingsInterface::class, MyAmazingShite::class);
```
there also a controller interface you may want to implement your own controller `Cacko\Yii2\Widgets\MonacoEditor\controllers\ControllerInterface`. 
