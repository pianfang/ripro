<?php

/**
 * WEB HOOK 消息通送 邮件推送
 * 触发使用 do_action('zb_send_mail_msg',array('email' => '', 'title' => '', 'msg' => ''));
 */

new ZB_MailHook();

class ZB_MailHook {

    public function __construct() {
        //埋点钩子
        add_action('zb_send_mail_msg', function ($param = array()) {
            $this->send_msg_mail($param);
        }, 10, 1);

        add_action('phpmailer_init',array($this,'smtp_mail'));
        add_filter('wp_mail',array($this,'mail_templates'),10,1);


    }

    private function send_get($url) {

        if (true) {
            //异步模式请求
            $headers = [
                'sslverify' => false,
                'blocking'  => false,
            ];
            $request  = ['url' => $url, 'type' => 'GET', 'headers' => $headers];
            try {
               $response = Requests::request_multiple([$request]);
            } catch (Exception $e) {
                // $e->getMessage();
            }
        } else {
            try {
                ////同步模式请求
                $response = wp_remote_get($url, array(
                    'timeout'   => 3,
                    'sslverify' => false,
                    'blocking'  => false, //异步执行 无需等待返回结果
                ));
            } catch (Exception $e) {
                // $e->getMessage();
            }
            
        }

        return true;
    }

    
    public function smtp_mail($phpmailer) {
        $phpmailer->FromName   = _cao('smtp_mail_nicname'); // 发件人昵称
        $phpmailer->Host       = _cao('smtp_mail_host'); // 邮箱SMTP服务器
        $phpmailer->Port       = (int) _cao('smtp_mail_port'); // SMTP端口，不需要改
        $phpmailer->Username   = _cao('smtp_mail_name'); // 邮箱账户
        $phpmailer->Password   = _cao('smtp_mail_passwd'); // 此处填写邮箱生成的授权码，不是邮箱登录密码
        $phpmailer->From       = _cao('smtp_mail_name'); // 邮箱账户同上
        $phpmailer->SMTPAuth   = !empty(_cao('smtp_mail_smtpauth'));
        $phpmailer->SMTPSecure = _cao('smtp_mail_smtpsecure'); // 端口25时 留空，465时 ssl，不需要改
        $phpmailer->IsSMTP();
    }

    public function send_msg_mail($param = array()) {
        if (empty($param)) {
            return false;
        }
        return wp_mail(
            $param['email'],
            $param['title'],
            $param['msg'],
            array('Content-Type: text/html; charset=UTF-8')
        );

    }


    public function mail_templates($mail){
        // var_dump($mail);die;
        return $mail;

    }

}



// 网站动态

class ZB_Dynamic{
    public static $max_num       = 12; //最大缓存动态数量
    public static $expire_time   = 5 * 24 * 3600; // 缓存一天
    public static $transient_key = 'ripro_site_dynamic'; //data keyex

    public static function get() {

        $data = get_transient(self::$transient_key);

        $arr = maybe_unserialize($data); //序列数组

        if ($arr === false || empty($arr) || !is_array($arr)) {
            return array();
        } else {
            // rsort($arr); //序列数组 sort
            return $arr;
        }

    }

    public static function add($param = array()) {

        $arr = self::get();

        $data = array_merge(array(
            'info' => '',
            'uid' => '',
            'href' => '',
            'time' => time(),
        ), $param);

        array_push($arr, $data);

        array_multisort(array_column($arr, 'time'), SORT_DESC, $arr);

        if (count($arr) > self::$max_num) {
            array_pop($arr);
        }

        $data_arr = maybe_serialize($arr); //格式化数据

        return set_transient(self::$transient_key, $data_arr, self::$expire_time);
    }

    public static function delete() {
        return delete_transient(self::$transient_key);
    }
}