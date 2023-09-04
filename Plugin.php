<?php

/**
 * 将Vaptcha集成到Typecho,有效防止各类机器人。
 *
 * @package Vaptcha
 * @author WhiteBear
 * @version 1.0.2
 * @link https://www.bearnotion.ru/
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
        echo '
        <style>
        .tips{
           border:1px solid  #000000; 
           padding:10px;
           border-radius:5px;
            line-height:3em;
        }
        </style>
        <div class="tips"><h3>Tips:在VID已填写完后的情况下：<br>
        <font color=red>评论区</font>:可在合适位置放入 &#60;?php $this->vaptchastyle(); ?><br><font color=red>登录/注册</font>:请在登录/注册按钮下方放入&#60;?php Vaptcha_Plugin::vaptcha(); ?><br><font color=red>v1.0.2版本</font>:支持了对不同按钮ID的适配，请在想要加入验证的页面的相应按钮下方放入&#60;?php Vaptcha_Plugin::vaptchaInit("按钮ID")； ?>，按钮ID填写当前按钮的ID，无需加#<br>使用<font color=red>vaptchaInit</font>方法的话所有页面均适用，且无需填写下方的按钮ID项</h3></div>';
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
      $vaptchastyle = '<div id="VAPTCHAContainer" style="width: 300px;height: 36px;">
        <div class="VAPTCHA-init-main">
            <div class="VAPTCHA-init-loading">
                <a href="/" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48px"
                        height="60px" viewBox="0 0 24 30"
                        style="enable-background: new 0 0 50 50; width: 14px; height: 14px; vertical-align: middle"
                        xml:space="preserve">
                        <rect x="0" y="9.22656" width="4" height="12.5469" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                        <rect x="10" y="5.22656" width="4" height="20.5469" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.15s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.15s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                        <rect x="20" y="8.77344" width="4" height="13.4531" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.3s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.3s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                    </svg>
                </a>
                <span class="VAPTCHA-text">Vaptcha Initializing...</span>
            </div>
        </div>
    </div>';   
        echo $vaptchastyle; 
     }
    /* 头部插入css */
    public static function header(){
        $VAPTCHA_style = "
               <style>
        .VAPTCHA-init-main {
            display: table;
            width: 100%;
            height: 100%;
            background-color: #eeeeee;
        }

        .VAPTCHA-init-loading {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .VAPTCHA-init-loading>a {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: none;
        }

        .VAPTCHA-init-loading .VAPTCHA-text {
            font-family: sans-serif;
            font-size: 12px;
            color: #cccccc;
            vertical-align: middle;
        }
    </style>
        ";
        echo $VAPTCHA_style;
    }

    /*  尾部加入js */
    public static function footer(){
        $options = Typecho_Widget::widget('Widget_Options')->plugin('Vaptcha');
        $vaptcha_js = '
            <script src="https://v-cn.vaptcha.com/v3.js"></script>
            <script>
                 document.getElementById("'.$options->button_id.'").setAttribute("disabled", true);
        vaptcha({
            vid: "'.$options->vid.'",
            mode: \'click\',
            scene: 0,
            container: "#VAPTCHAContainer",
            area: \'auto\',
        }).then(function (VAPTCHAObj) {
            // 将VAPTCHA验证实例保存到局部变量中
            obj = VAPTCHAObj;

            // 渲染验证组件
            VAPTCHAObj.render();

            // 验证成功进行后续操作
            VAPTCHAObj.listen(\'pass\', function () {
                document.getElementById("'.$options->button_id.'").removeAttribute("disabled");
            })
        })
            </script>
        ';
        echo $vaptcha_js;
    }
    
    public static function vaptcha(){
        $options = Typecho_Widget::widget('Widget_Options')->plugin('Vaptcha');
      $vaptchastyle = '
      <script src="https://v-cn.vaptcha.com/v3.js"></script>
   <style>
        .VAPTCHA-init-main {
            display: table;
            width: 100%;
            height: 100%;
            background-color: #eeeeee;
        }

        .VAPTCHA-init-loading {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .VAPTCHA-init-loading>a {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: none;
        }

        .VAPTCHA-init-loading .VAPTCHA-text {
            font-family: sans-serif;
            font-size: 12px;
            color: #cccccc;
            vertical-align: middle;
        }
    </style>
      
      <div id="VAPTCHAContainer" style="width: 300px;height: 36px;">
        <div class="VAPTCHA-init-main">
            <div class="VAPTCHA-init-loading">
                <a href="/" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48px"
                        height="60px" viewBox="0 0 24 30"
                        style="enable-background: new 0 0 50 50; width: 14px; height: 14px; vertical-align: middle"
                        xml:space="preserve">
                        <rect x="0" y="9.22656" width="4" height="12.5469" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                        <rect x="10" y="5.22656" width="4" height="20.5469" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.15s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.15s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                        <rect x="20" y="8.77344" width="4" height="13.4531" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.3s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.3s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                    </svg>
                </a>
                <span class="VAPTCHA-text">Vaptcha Initializing...</span>
            </div>
        </div>
    </div>

    <script>
    document.getElementById("'.$options->button_id.'").setAttribute("disabled", true);
        vaptcha({
            vid: "'.$options->vid.'",
            mode: \'click\',
            scene: 0,
            container: "#VAPTCHAContainer",
            area: \'auto\',
        }).then(function (VAPTCHAObj) {
            // 将VAPTCHA验证实例保存到局部变量中
            obj = VAPTCHAObj;

            // 渲染验证组件
            VAPTCHAObj.render();

            // 验证成功进行后续操作
            VAPTCHAObj.listen(\'pass\', function () {
                document.getElementById("'.$options->button_id.'").removeAttribute("disabled");
            })
        })
    </script>
      
      
      ';   
        echo $vaptchastyle; 
     }
     
     public static function vaptchaInit($id){
        $options = Typecho_Widget::widget('Widget_Options')->plugin('Vaptcha');
      $vaptchastyle = '
      <script src="https://v-cn.vaptcha.com/v3.js"></script>
   <style>
        .VAPTCHA-init-main {
            display: table;
            width: 100%;
            height: 100%;
            background-color: #eeeeee;
        }

        .VAPTCHA-init-loading {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .VAPTCHA-init-loading>a {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: none;
        }

        .VAPTCHA-init-loading .VAPTCHA-text {
            font-family: sans-serif;
            font-size: 12px;
            color: #cccccc;
            vertical-align: middle;
        }
    </style>
      
      <div id="VAPTCHAContainer" style="width: 300px;height: 36px;">
        <div class="VAPTCHA-init-main">
            <div class="VAPTCHA-init-loading">
                <a href="/" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48px"
                        height="60px" viewBox="0 0 24 30"
                        style="enable-background: new 0 0 50 50; width: 14px; height: 14px; vertical-align: middle"
                        xml:space="preserve">
                        <rect x="0" y="9.22656" width="4" height="12.5469" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                        <rect x="10" y="5.22656" width="4" height="20.5469" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.15s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.15s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                        <rect x="20" y="8.77344" width="4" height="13.4531" fill="#CCCCCC">
                            <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.3s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                            <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.3s" dur="0.6s"
                                repeatCount="indefinite"></animate>
                        </rect>
                    </svg>
                </a>
                <span class="VAPTCHA-text">Vaptcha Initializing...</span>
            </div>
        </div>
    </div>

    <script>
    document.getElementById("'.$id.'").setAttribute("disabled", true);
        vaptcha({
            vid: "'.$options->vid.'",
            mode: \'click\',
            scene: 0,
            container: "#VAPTCHAContainer",
            area: \'auto\',
        }).then(function (VAPTCHAObj) {
            // 将VAPTCHA验证实例保存到局部变量中
            obj = VAPTCHAObj;

            // 渲染验证组件
            VAPTCHAObj.render();

            // 验证成功进行后续操作
            VAPTCHAObj.listen(\'pass\', function () {
                document.getElementById("'.$id.'").removeAttribute("disabled");
            })
        })
    </script>
      
      
      ';   
        echo $vaptchastyle; 
     }
}
