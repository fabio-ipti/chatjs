<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CommonAsset extends AssetBundle
{
   public function init() {
        parent::init();
        //Deixar False quando criar as releases
        $this->publishOptions['forceCopy'] = false;
    }
    public $basePath = '@webroot';
    //public $baseUrl = '@web';
    //public $sourcePath = '@web';

    public $css = [

    ];
    public $js = [
      'js/jQueryPopMenu/jquery.popmenu.min.js',
      'js/common.js'
    ];
    public $depends = [
      'yii\web\YiiAsset'
    ];

}
