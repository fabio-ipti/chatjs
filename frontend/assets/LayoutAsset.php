<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LayoutAsset extends AssetBundle
{

   public function init() {
        parent::init();
        //Deixar False quando criar as releases
        $this->publishOptions['forceCopy'] = false;
    }
    //public $basePath = '@webroot';
    //public $baseUrl = '@web';

    public $sourcePath = '@bower';
    public $css = [
      'admin-lte/bootstrap/css/bootstrap.min.css',
      'admin-lte/dist/css/icons/ionicons.min.css',
      'admin-lte/plugins/select2/select2.min.css',
      'admin-lte/dist/css/AdminLTE.min.css',
      'admin-lte/dist/css/skins/_all-skins.min.css',
      'admin-lte/dist/css/fonts/font-awesome.min.css',
      'admin-lte/plugins/iCheck/flat/blue.css',
      'admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.css',
      'admin-lte/plugins/datepicker/datepicker3.css',
      'admin-lte/plugins/daterangepicker/daterangepicker.css',
      'admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
      'admin-lte/plugins/iCheck/square/blue.css',
      'admin-lte/plugins/iCheck/flat/_all.css',
      'admin-lte/plugins/datatables/dataTables.bootstrap.css',
      'admin-lte/dist/css/skins/_all-skins.min.css',

      'sweetalert/dist/sweetalert.css',
    ];
    public $js = [
      //'admin-lte/plugins/jQuery/jquery-2.2.3.min.js',
      'admin-lte/plugins/jQueryUI/jquery-ui.min.js',
      'admin-lte/dist/js/global.js',
      'admin-lte/bootstrap/js/bootstrap.min.js',
      'admin-lte/plugins/sparkline/jquery.sparkline.min.js',
      'admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
      'admin-lte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
      'admin-lte/plugins/knob/jquery.knob.js',
      'admin-lte/plugins/daterangepicker/moment.min.js',
      'admin-lte/plugins/daterangepicker/daterangepicker.js',
      'admin-lte/plugins/datepicker/bootstrap-datepicker.js',
      'admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
      'admin-lte/plugins/slimScroll/jquery.slimscroll.min.js',
      'admin-lte/plugins/fastclick/fastclick.js',
      'admin-lte/dist/js/app.min.js',
      'admin-lte/dist/js/demo.js',
      'admin-lte/plugins/datatables/jquery.dataTables.js',
      'admin-lte/plugins/datatables/dataTables.bootstrap.min.js',
      'admin-lte/plugins/slimScroll/jquery.slimscroll.min.js',
      'admin-lte/plugins/iCheck/icheck.min.js',
      'admin-lte/plugins/select2/select2.full.min.js',

      'sweetalert/dist/sweetalert.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];



}
