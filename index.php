<?php
/**
 * Index
 * 
 * @package    TestWork
 * @subpackage Includes
 * @category   index
 * @author     Misha (Mik™) <miksoft.tm@gmail.com> <miksrv.ru>
 */

define('PATH', str_replace(basename(__FILE__), '', __FILE__));

include_once PATH . 'php.inc/init.php';

$user_list = get_user_list();

?><!DOCTYPE html>
<html lang="ru">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width; initial-scale=1;" />
        <title>PHP Developers contest</title>
        <link type="text/css" rel="stylesheet" href="/assets/css/main.css" />
        <link type="text/css" rel="stylesheet" href="/assets/css/fontawesome-all.min.css" />
        <script type="text/javascript" src="/assets/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/assets/js/app.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <section id="header">
                <aside class="logo">
                    <img src="/assets/img/php_elephant.svg" alt="" />
                    <strong>PHP</strong>
                    <span>Developers contest</span>
                </aside>
                <main>
                    <a href="javascript://void(0);" id="star-switch" class="favorite" title="" onclick="return App.Func.Favorite();"><i class="fas fa-star"></i></a>
                    <div class="form-element">
                        <input type="text" id="message" placeholder="Сообщение..." value="" onkeypress="return App.Func.KeyPress(event)" />
                        <button class="btn btn-telegram" onclick="return App.Func.Send();">
                            <i class="fab fa-telegram-plane"></i>
                        </button>
                    </div>
                </main>
            </section>
            <section id="content">
                <aside class="user-list">
                    <?php if (is_array($user_list) && ! empty($user_list)): ?>
                    <ul>
                        <?php foreach ($user_list as $item): ?>
                        <?php include(PATH . 'views/user.php'); ?>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </aside>
                <main class="chat-frame">
                    <div id="chat-content">
                        <div class="wellcome">Выберите диалог</div>
                    </div>
                </main>
            </section>
        </div>
    </body>
</html>