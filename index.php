<?php
namespace docker {
    function adminer_object() {
        require_once('plugins/plugin.php');

        class Adminer extends \AdminerPlugin {
            function _callParent($function, $args) {
                if ($function === 'loginForm') {
                    ob_start();
                    $return = \Adminer::loginForm();
                    $form = ob_get_clean();

                    $form .= "<script nonce=\"".get_nonce()."\">var envs = {
    ADMINER_AUTOLOGIN: ".($_ENV['ADMINER_AUTOLOGIN'] ?? 'false').",
    ADMINER_DB: '".($_ENV['ADMINER_DB'] ?? 'db')."',
    ADMINER_DRIVER: '".($_ENV['ADMINER_DRIVER'] ?? 'mysql')."',
    ADMINER_SERVER: '".($_ENV['ADMINER_SERVER'])."',
    ADMINER_USERNAME: '".($_ENV['ADMINER_USERNAME'])."',
    ADMINER_PASSWORD: '".($_ENV['ADMINER_PASSWORD'])."',
}
document.querySelectorAll('form [name]').forEach((item) => {
    const key = 'ADMINER_'+item.name.split('[')[1].split(']')[0].toUpperCase();
    if (key == 'ADMINER_DRIVER' && envs[key] == 'mysql') {
        item.value = 'server';
        return;
    }

    if (envs[key]) {
        item.value = envs[key];
    }
})
if (envs['ADMINER_AUTOLOGIN'] && window.location.search == '') {
    document.querySelectorAll('form')[0].submit()
}</script><style>#menu {display:none}#content{width: 100%;margin: 50px auto 0;}</style>";

                    echo $form;

                    return $return;
                }

                return parent::_callParent($function, $args);
            }
        }

        $plugins = [];
        foreach (glob('plugins-enabled/*.php') as $plugin) {
            $plugins[] = require($plugin);
        }

        return new Adminer($plugins);
    }
}

namespace {
    if (basename($_SERVER['DOCUMENT_URI'] ?? $_SERVER['REQUEST_URI']) === 'adminer.css' && is_readable('adminer.css')) {
        header('Content-Type: text/css');
        readfile('adminer.css');
        exit;
    }

    function adminer_object() {
        return \docker\adminer_object();
    }

    require('adminer.php');
}
