<?php

/**
 * 异常友链检查（后台管理->友链检查）
 * @author 湘铭呀！
 * @package Linkdetection
 * @version 1.0.4
 * @link https://xiangming.site/
 */

class Linkdetection_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Helper::addAction('Linkdetection_action', 'Linkdetection_Action');
        Helper::addPanel(3, 'Linkdetection/Check.php', '友链检查', '友链检查', 'administrator');
    }
    
    public static function deactivate()
    {
        Helper::removeAction('Linkdetection_action');
        Helper::removePanel(3, 'Linkdetection/Check.php');
    }
    
    public static function config(Typecho_Widget_Helper_Form $form)
    {
    }
    
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
    
    public static function render()
    {
    }
}
