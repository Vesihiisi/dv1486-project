<?php

require __DIR__.'/config_with_app.php';

$app->navbar->configure(ANAX_APP_PATH . 'config/navbar01.php');
$app->theme->configure(ANAX_APP_PATH . 'config/theme-grid.php');
$app->session();

// Create services and inject into the app.
// This is the service for comments
$di->set('CommentController', function() use ($di) { 
    $controller = new Anax\Comment\CommentController(); 
    $controller->setDI($di); 
    return $controller; 
});

// This is the service for users
$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('AdminController', function() use ($di) {
    $controller = new \Anax\Admin\AdminController();
    $controller->setDI($di);
    return $controller;
});


$app->router->add('', function() use ($app) {

    $app->theme->setTitle("Welcome");

        $app->dispatcher->forward([
        'controller' => 'comment',
        'action'     => 'viewNewest',
        'params' => ["question", 4, "triptych-1"],
        ]);

        $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'viewMostReputation',
        'params' => [4, "triptych-2"],
        ]);

        $app->dispatcher->forward([
        'controller' => 'comment',
        'action'     => 'viewMostCommonTags',
        'params' => [4, "triptych-3"],
        ]);

        $app->views->add('default/article', [
        'content' => $app->fileContent->get('front/front_1.html'),
        ], "full");

});
 
 
$app->router->add('comment', function() use ($app) {




    $app->dispatcher->forward([
        'controller' => 'comment',
        'action'     => 'view',
        ]);

    $app->theme->setTitle("All questions"); 
 
});

$app->router->add('tag', function() use ($app) {

    $app->theme->setTitle("Tags");

    $app->dispatcher->forward([
        'controller' => 'comment',
        'action'     => 'viewTags',
    ]);

});


$app->router->add('users', function() use ($app) {

    $app->theme->setTitle("Users");

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'list',
        ]);

});

$app->router->add('admin', function() use ($app) {

    $app->theme->setTitle("Admin panel"); 

    $app->dispatcher->forward([
        'controller' => 'admin',
        'action'     => 'view',
        ]);
});



$app->router->add('about', function() use ($app) {

    $app->theme->setTitle("About");

    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('me/page', [
        'content' => $content,
    ]);
});




$app->router->add('setup', function() use ($app) {

    $app->theme->setTitle("Restore database");

    $app->dispatcher->forward([
        'controller' => 'admin',
        'action'     => 'setup',
        ]);


});



$app->router->handle();
$app->theme->render();
