<?php

namespace app\controllers\admin;

use app\models\User;
use ishop\libs\Pagination;

//контроллер для додавання/редагування/видалення усіх користувачів сайту
class UserController extends AppController {

    public function indexAction(){
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perpage = 3;
        $count = \R::count('user');
        $pagination = new Pagination($page, $perpage, $count);
        $start = $pagination->getStart();
        $users = \R::findAll('user', "LIMIT $start, $perpage");
        $this->setMeta('Список користувачів');
        $this->set(compact('users', 'pagination', 'count'));
    }

    public function addAction(){
        $this->setMeta('Новий користувач');
    }

    public function editAction(){
        if(!empty($_POST)){
            $id = $this->getRequestID(false);
            $user = new \app\models\admin\User();
            $data = $_POST;
            $user->load($data);
            if(!$user->attributes['password']){
                unset($user->attributes['password']);
            }else{
                $user->attributes['password'] = password_hash($user->attributes['password'], PASSWORD_DEFAULT);
            }
            if(!$user->validate($data) || !$user->checkUnique()){
                $user->getErrors();
                redirect();
            }
            if($user->update('user', $id)){
                $_SESSION['success'] = 'Зміни збережено';
            }
            redirect();
        }

        $user_id = $this->getRequestID();
        $user = \R::load('user', $user_id);

        $orders = \R::getAll("SELECT `order`.`id`, `order`.`user_id`, `order`.`status`, `order`.`date`, `order`.`update_at`, `order`.`currency`, ROUND(SUM(`order_product`.`price`), 2) AS `sum` FROM `order`
  JOIN `order_product` ON `order`.`id` = `order_product`.`order_id`
  WHERE user_id = {$user_id} GROUP BY `order`.`id` ORDER BY `order`.`status`, `order`.`id`");

        $this->setMeta('Редагування профілю користувача');
        $this->set(compact('user', 'orders'));
    }
    //авторизація адміна
    public function loginAdminAction(){
        if(!empty($_POST)){
            $user = new User();
            if(!$user->login(true)){
                $_SESSION['error'] = 'Логін чи пароль невірні';
            }
            if(User::isAdmin()){
                redirect(ADMIN);
            }else{
                redirect();
            }
        }
        $this->layout = 'login';
    }

}