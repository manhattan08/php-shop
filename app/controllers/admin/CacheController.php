<?php

namespace app\controllers\admin;

use ishop\Cache;

//контролер очистки кешу сайту(для адміна)
class CacheController extends AppController {

    public function indexAction(){
        $this->setMeta('Очистка кешу');
    }

    public function deleteAction(){
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        $cache = Cache::instance();
        switch($key){
            case 'category':
                $cache->delete('cats');
                $cache->delete('ishop_menu');
                break;
            case 'filter':
                $cache->delete('filter_group');
                $cache->delete('filter_attrs');
                break;
        }
        $_SESSION['success'] = 'Вибраний кеш видалено';
        redirect();
    }

}