<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

class mh_menu extends ecjia_merchant
{
    public function __construct()
    {
        parent::__construct();

        // RC_Loader::load_app_func('global');
        // Ecjia\App\Wechat\Helper::assign_adminlog_content();

        /* 加载全局 js/css */
        RC_Script::enqueue_script('smoke');
        RC_Script::enqueue_script('jquery-validate');
        RC_Script::enqueue_script('jquery-form');
        RC_Script::enqueue_script('menu', RC_App::apps_url('statics/js/menu.js', __FILE__), array(), false, true);
        RC_Style::enqueue_style('menu', RC_App::apps_url('statics/css/menu.css', __FILE__));

        RC_Script::localize_script('wechat_menus', 'js_lang', RC_Lang::get('wechat::wechat.js_lang'));
        ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('自定义菜单', RC_Uri::url('toutiao/mh_menu/init')));

        ecjia_merchant_screen::get_current_screen()->set_subject('自定义菜单');
    }

    public function init()
    {
        $this->admin_priv('toutiao_manage');

        ecjia_merchant_screen::get_current_screen()->remove_last_nav_here();
        ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('自定义菜单'));
        $this->assign('ur_here', '店铺自定义菜单');

        $this->assign('form_action', RC_Uri::url('toutiao/mh_menu/insert'));
        $this->assign('edit_url', RC_Uri::url('toutiao/mh_menu/get_menu_info'));
        $this->assign('del_url', RC_Uri::url('toutiao/mh_menu/remove'));
        $this->assign('check_url', RC_Uri::url('toutiao/mh_menu/check'));

        $list = $this->get_menuslist();
        $this->assign('menu_list', $list['menu_list']);

        $count = count($list['menu_list']);
        $this->assign('count', $count);

        $this->display('toutiao_menu_list.dwt');
    }

    public function insert()
    {
        $this->admin_priv('toutiao_update', ecjia::MSGTYPE_JSON);

        $wechat_id = 999;

        $pid  = !empty($_POST['pid']) ? intval($_POST['pid']) : 0;
        $name = !empty($_POST['name']) ? trim($_POST['name']) : !empty($pid) ? '子菜单名称' : '菜单名称';

        $type    = !empty($_POST['type']) ? $_POST['type'] : 'click';
        $key     = !empty($_POST['key']) ? $_POST['key'] : '';
        $web_url = !empty($_POST['url']) ? $_POST['url'] : '';

        $status = !empty($_POST['status']) ? intval($_POST['status']) : 1;
        $sort   = !empty($_POST['sort']) ? intval($_POST['sort']) : 0;

        if ($type == 'view') {
            if (!empty($web_url)) {
                $url = $web_url;
            }
        } else {
            //小程序配置信息
            $h5_url      = RC_Uri::home_url() . '/sites/m/';
            $weapp_appid = $_POST['weapp_appid'];
            if (!empty($weapp_appid)) {
                $miniprogram_config = array(
                    'url'      => $h5_url,
                    'appid'    => $weapp_appid,
                    'pagepath' => 'pages/ecjia-store/ecjia',
                );
                $url = serialize($miniprogram_config);
            }
        }

        $data = array(
            'wechat_id' => $wechat_id,
            'pid'       => $pid,
            'name'      => $name,
            'type'      => $type,
            'key'       => $key,
            'url'       => $url,
            'status'    => $status,
            'sort'      => $sort,
        );
        $id = RC_DB::table('wechat_menu')->insertGetId($data);
        $this->admin_log($_POST['name'], 'add', 'menu');

        if (!empty($pid)) {
            RC_DB::table('wechat_menu')->where('wechat_id', $wechat_id)->where('id', $pid)->update(array('type' => '', 'key' => '', 'url' => ''));
        }

        $listdb = $this->get_menuslist();
        $this->assign('menu_list', $listdb['menu_list']);

        $count = count($listdb['menu_list']);
        $this->assign('count', $count);

        $this->assign('id', $id);
        $this->assign('pid', $pid);

        $res = $this->fetch('library/toutiao_menu_menu.lbi');

        if ($type == 'miniprogram') {
            $config_url     = unserialize($data['url']);
            $data['app_id'] = $config_url['appid'];
        }
        $this->assign('wechat_menus', $data);

        $result = $this->fetch('library/toutiao_menu_sub.lbi');
        return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('data' => $res, 'result' => $result));
    }

    /**
     * 编辑菜单处理
     */
    public function update()
    {
        $this->admin_priv('wechat_menus_update', ecjia::MSGTYPE_JSON);

        $wechat_id = 999;

        $id      = !empty($_POST['id']) ? intval($_POST['id']) : 0;
        $name    = !empty($_POST['name']) ? trim($_POST['name']) : '';
        $type    = !empty($_POST['type']) ? $_POST['type'] : '';
        $key     = !empty($_POST['key']) ? $_POST['key'] : '';
        $web_url = !empty($_POST['url']) ? $_POST['url'] : '';
        $status  = !empty($_POST['status']) ? intval($_POST['status']) : 0;
        $sort    = !empty($_POST['sort']) ? intval($_POST['sort']) : 0;

        if (empty($name)) {
            return $this->showmessage('菜单名称不能为空', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        if ($type == 'view') {
            if (empty($web_url)) {
                return $this->showmessage('外链url不能为空', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            } else {
                if (strpos($web_url, 'http://') === false && strpos($web_url, 'https://') === false) {
                    return $this->showmessage('外链url格式错误', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
                }
                $url = $web_url;
            }
        } elseif ($type == 'miniprogram') {
            //小程序配置信息
            $h5_url      = RC_Uri::home_url() . '/sites/m/';
            $weapp_appid = $_POST['weapp_appid'];
            if (empty($weapp_appid)) {
                return $this->showmessage('请选择小程序', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            } else {
                $miniprogram_config = array(
                    'url'      => $h5_url,
                    'appid'    => $weapp_appid,
                    'pagepath' => 'pages/ecjia-store/ecjia',
                );
                $url = serialize($miniprogram_config);
            }
        }
        $data = array(
            'name'   => $name,
            'type'   => $type,
            'key'    => $key,
            'url'    => $url,
            'status' => $status,
            'sort'   => $sort,
        );
        RC_DB::table('wechat_menu')->where('id', $id)->where('wechat_id', $wechat_id)->update($data);

        $this->admin_log($name, 'edit', 'menu');

        $listdb    = $this->get_menuslist();
        $menu_list = $listdb['menu_list'];
        $count     = count($listdb['menu_list']);

        $this->assign('menu_list', $menu_list);
        $this->assign('count', $count);

        $info = RC_DB::table('wechat_menu')->where('id', $id)->where('wechat_id', $wechat_id)->first();
        if ($type == 'miniprogram') {
            $config_url     = unserialize($info['url']);
            $info['app_id'] = $config_url['appid'];
        }

        $count = RC_DB::table('wechat_menu')->where('wechat_id', $wechat_id)->where('pid', $info['id'])->count();

        $this->assign('id', $id);
        $this->assign('pid', $info['pid']);
        $this->assign('wechat_menus', $info);

        $res = $this->fetch('library/toutiao_menu_menu.lbi');

        if ($wechat_menus['pid'] == 0 && $count != 0) {
            $result = $this->fetch('library/toutiao_menu_second.lbi');
        } else {
            $result = $this->fetch('library/toutiao_menu_sub.lbi');
        }

        return $this->showmessage('保存成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('data' => $res, 'result' => $result));
    }

    public function check()
    {
        $wechat_id = 999;

        $listdb    = $this->get_menuslist();
        $menu_list = $listdb['menu_list'];

        $id = 0;
        if (!empty($menu_list)) {
            foreach ($menu_list as $k => $v) {
                if ($v['type'] == 'click') {
                    if (empty($v['key']) && empty($v['sub_button'])) {
                        $id = $v['id'];
                        break;
                    } else if (!empty($v['sub_button'])) {
                        foreach ($v['sub_button'] as $key => $val) {
                            if ($val['type'] == 'click') {
                                if (empty($val['key'])) {
                                    $id = $val['id'];
                                    break;
                                }
                            } else if ($val['type'] == 'view' || $val['type'] == 'miniprogram') {
                                if (empty($val['url'])) {
                                    $id = $val['id'];
                                    break;
                                }
                            }
                        }
                    }
                } else if (($v['type'] == 'view' || $v['type'] == 'miniprogram')) {
                    if (empty($v['url']) && empty($v['sub_button'])) {
                        $id = $v['id'];
                        break;
                    }
                    if (!empty($v['sub_button'])) {
                        foreach ($v['sub_button'] as $key => $val) {
                            if ($val['type'] == 'click') {
                                if (empty($val['key'])) {
                                    $id = $val['id'];
                                    break;
                                }
                            } else if ($val['type'] == 'view' || $val['type'] == 'miniprogram') {
                                if (empty($val['url'])) {
                                    $id = $val['id'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            $count = count($listdb['menu_list']);
            $data  = RC_DB::table('wechat_menu')->where('wechat_id', $wechat_id)->where('id', $id)->first();
            if ($data['type'] == 'miniprogram') {
                $config_url     = unserialize($data['url']);
                $data['app_id'] = $config_url['appid'];
            }

            $this->assign('menu_list', $listdb['menu_list']);
            $this->assign('count', $count);

            $this->assign('id', $id);
            $this->assign('pid', $data['pid']);
            $this->assign('wechat_menus', $data);

            $res = $this->fetch('library/toutiao_menu_menu.lbi');

            $result = $this->fetch('library/toutiao_menu_sub.lbi');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('data' => $res, 'result' => $result, 'id' => $id));
        }
    }

    public function get_menu_info()
    {
        $wechat_id = 999;

        $id = intval($_POST['id']);
        $this->assign('id', $id);

        $wechat_menus = RC_DB::table('wechat_menu')->where('wechat_id', $wechat_id)->where('id', $id)->first();
        if ($wechat_menus['type'] == 'miniprogram') {
            $config_url             = unserialize($wechat_menus['url']);
            $wechat_menus['app_id'] = $config_url['appid'];
        }
        $this->assign('wechat_menus', $wechat_menus);

        $count = RC_DB::table('wechat_menu')->where('wechat_id', $wechat_id)->where('pid', $wechat_menus['id'])->count();
        if ($count != 0) {
            $data = $this->fetch('library/toutiao_menu_second.lbi');
        } else {
            $data = $this->fetch('library/toutiao_menu_sub.lbi');
        }
        return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('data' => $data));
    }

    private function get_menuslist()
    {
        $wechat_id = 999;

        $list   = RC_DB::table('wechat_menu')->where('wechat_id', $wechat_id)->orderBy('sort', 'asc')->get();
        $result = array();

        if (!empty($list)) {
            foreach ($list as $vo) {
                if ($vo['type'] == 'miniprogram') {
                    $config_url = unserialize($vo['url']);
                    $vo['url']  = $config_url['pagepath'];
                }
                if ($vo['pid'] == 0) {
                    $sub_button = array();
                    foreach ($list as $val) {
                        if ($vo['id'] == $val['pid']) {
                            if ($val['type'] == 'miniprogram') {
                                $child_url  = unserialize($val['url']);
                                $val['url'] = $child_url['pagepath'];
                            }
                            $sub_button[] = $val;
                        }
                    }
                    $vo['sub_button'] = $sub_button;
                    $result[]         = $vo;
                }
            }
        }

        if (!empty($result)) {
            foreach ($result as $k => $v) {
                $result[$k]['count'] = 0;
                if (!empty($v['sub_button'])) {
                    $result[$k]['count'] = count($v['sub_button']);
                }
            }
        }

        return array('menu_list' => $result);
    }

}

//end
