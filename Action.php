<?php
session_start();
class Linkdetection_Action extends Widget_Abstract_Contents implements Widget_Interface_Do
{
    public function action()
    {
        $user = Typecho_Widget::widget('Widget_User');
        if (!$user->pass('administrator')) {
            die('未登录用户!');
        }
        if ($_GET['action'] === 'links') {
            $db = Typecho_Db::get();
            $links = $db->fetchAll($db->select('lid', 'name', 'url')->from('table.links')->where('sort != ?', 'others'));
            echo json_encode($links);
        }
        if ($_GET['action'] === 'del') {
            $dates = json_decode($_POST['data']);
            $db = Typecho_Db::get();
            foreach ($dates as $date) {
                $update = $db->update('table.links')->rows(array('sort' => 'others'))->where('lid = ?', $date);
                $db->query($update);
            }
        }
        if ($_GET['action'] === 'post') {
            $_SESSION['data'] = $_POST['data'];
        }
        if ($_GET['action'] === 'check') {
            $url = $_POST['url'];
            $status = $this->checkUrl($url);
            echo $status;
        }
    }

    private function checkUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 允许重定向
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode ? $httpCode : '无状态码返回';
    }
}
