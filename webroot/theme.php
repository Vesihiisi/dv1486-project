<?php 
/**
 * This is a Anax pagecontroller.
 *
 */
require __DIR__.'/config_with_app.php';


$app->navbar->configure(ANAX_APP_PATH . 'config/navbar-grid.php');
$app->theme->configure(ANAX_APP_PATH . 'config/theme-grid.php');


$app->router->add('regions', function() use ($app) {
    $app->theme->addStylesheet('css/anax-grid/regions_demo.css');
    $app->theme->setTitle("Regions");
 
    $app->views->addString('flash', 'flash')
               ->addString('featured-1', 'featured-1')
               ->addString('featured-2', 'featured-2')
               ->addString('featured-3', 'featured-3')
               ->addString('main', 'main')
               ->addString('sidebar', 'sidebar')
               ->addString('triptych-1', 'triptych-1')
               ->addString('triptych-2', 'triptych-2')
               ->addString('triptych-3', 'triptych-3')
               ->addString('footer-col-1', 'footer-col-1')
               ->addString('footer-col-2', 'footer-col-2')
               ->addString('footer-col-3', 'footer-col-3')
               ->addString('footer-col-4', 'footer-col-4');
});


$app->router->add('', function() use ($app) {
    $app->theme->setTitle("Typography test");
    $content = $app->fileContent->get('typography.html');
    $content_short = $app->fileContent->get('typography_short.html');


    $app->views->add('default/article', [
        'content' => $content,
    ]);

    $app->views->add('default/article', [
        'content' => $content,
    ], "sidebar");

    $app->views->add('default/article', [
        'content' => $content_short,
    ], "featured-1");

    $app->views->add('default/article', [
        'content' => $content_short,
    ], "featured-2");

    $app->views->add('default/article', [
        'content' => $content_short,
    ], "featured-3");
});

$app->router->add('fa', function() use ($app) {
        
    $app->theme->setTitle("Font Awesome test");
    $app->views->add('me/theme/fa1', [], 'main');
    $app->views->add('me/theme/fa-sidebar', [], 'sidebar');
    $app->views->add('me/theme/fa-flash', [], 'flash');
});


$app->router->add('source', function() use ($app) {
    $app->theme->addStylesheet('css/source.css');
    $app->theme->setTitle("Source");
 
    $source = new \Mos\Source\CSource([
        'secure_dir' => '..', 
        'base_dir' => '..', 
        'add_ignore' => ['.htaccess'],
    ]);
 
    $app->views->add('me/source', [
        'content' => $source->View(),
    ], 'main');
 
});

$app->router->handle();
$app->theme->render();
