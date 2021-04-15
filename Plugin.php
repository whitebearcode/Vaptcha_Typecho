<?php

/**
 * 将Vaptcha集成到Typecho,有效防止各类机器人。
 *
 * @package Vaptcha
 * @author WhiteBear
 * @version 1.0.0
 * @link https://www.coder-bear.com/
 */
class Vaptcha_Plugin implements Typecho_Plugin_Interface{

    /* 激活插件方法 */
    public static function activate(){
        
        Typecho_Plugin::factory('Widget_Archive')->header = array('Vaptcha_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Vaptcha_Plugin', 'footer');
        Typecho_Plugin::factory('Widget_Archive')->___vaptchastyle = array('Vaptcha_Plugin', 'vaptchastyle');
        return _t('插件已启用');
    }

    /* 禁用插件方法 */
    public static function deactivate(){
        return _t('插件已禁用');
    }

    /* 插件配置方法 */
    public static function config(Typecho_Widget_Helper_Form $form){
        echo '<h3>Tips:以下填写完毕后请在模板合适位置放入 &#60;?php $this->vaptchastyle(); ?></h3>';
        $vid = new Typecho_Widget_Helper_Form_Element_Text('vid', NULL, '****', _t('VID'), _t("Vaptcha验证单元id"));
        $form->addInput($vid);
        $button_id = new Typecho_Widget_Helper_Form_Element_Text('button_id', NULL, 'check', _t("按钮"), _t("请在模板表单(如评论)提交按钮button中添加id,如<img width=\"320px\" src=\"/usr/plugins/Vaptcha/img/tip.png\">，并将填写的ID复制到本栏，验证前将禁止点击该按钮，验证成功后将允许点击该按钮"));
        $form->addInput($button_id);
    }

    /* 个人用户的配置面板 */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }
    
    /* 样式 */
     public static function vaptchastyle(){
      $vaptchastyle = '<div class="vaptcha-container" style="width: 300px;height: 36px;">
<div class="vaptcha-init-main">
    <div class="vaptcha-init-loading">
    <a href="/" target="_blank">
        <img src="https://r.vaptcha.net/public/img/vaptcha-loading.gif" />
    </a>
    <span class="vaptcha-text">人机验证正在加载....</span>
    </div>
</div>
</div>';   
        echo $vaptchastyle; 
     }
    /* 头部插入css */
    public static function header(){
        $VAPTCHA_style = "
            <style>
                .vaptcha-container {
                    width: 100%;
                    height: 36px;
                    line-height: 36px;
                    text-align: center;
                }

                .vaptcha-init-main {
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: #EEEEEE;
                }

               .vaptcha-init-loading {
                    display: table-cell;
                    vertical-align: middle;
                    text-align: center
                }

               .vaptcha-init-loading>a {
                    display: inline-block;
                    width: 18px;
                    height: 18px;
                    border: none;
                }

               .vaptcha-init-loading>a img {
                    vertical-align: middle
                }

               .vaptcha-init-loading .vaptcha-text {
                    font-family: sans-serif;
                    font-size: 12px;
                    color: #CCCCCC;
                    vertical-align: middle
                }

            </style>
        ";
        echo $VAPTCHA_style;
    }

    /*  尾部加入js */
    public static function footer(){
        $options = Typecho_Widget::widget('Widget_Options')->plugin('Vaptcha');
        $vaptcha_js = "
            <script src=\"https://v.vaptcha.com/v3.js\"></script>
            <script>
                document.getElementById(\"".$options->button_id."\").setAttribute(\"disabled\", true);
                vaptcha({
                    vid: '".$options->vid."', // 验证单元id
                    type: 'click', // 显示类型 点击式
                    container: '.vaptcha-container' // 按钮容器，可为Element 或者 selector
                }).then(function (vaptchaObj) {
                    vaptchaObj.listen('pass', function() {
                        document.getElementById(\"".$options->button_id."\").removeAttribute(\"disabled\");
                    })
                    vaptchaObj.render()
                })
            </script>
        ";
        echo $vaptcha_js;
    }
}
