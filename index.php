<?php
require_once 'vendor/autoload.php';

use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Cria o objeto Twig
$loader = new FilesystemLoader(__DIR__.'/views');
$twig = new Environment($loader);

// Função para tratar as rotas
function dispatch(ServerRequestInterface $request, Environment $twig)
{
    $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
        // Defina suas rotas aqui
        $r->get('/', 'home');
        $r->get('/levels', 'levels');
        $r->get('/training-room', 'trainingRoom');
    });

    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // Rota não encontrada
            echo "404 - Rota não encontrada";
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            // Método não permitido para a rota
            echo "405 - Método não permitido";
            break;
        case FastRoute\Dispatcher::FOUND:
            // Rota encontrada, chama o manipulador
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            call_user_func($handler, $twig, $vars);
            break;
    }
}

// Funções para cada rota
function home(Environment $twig)
{
    echo $twig->render('home.html.twig',array(
        'title' => 'Início',
        'subtitle' =>'Descubra a arte de harmonizar sua percepção musical.'
    ));
}

function levels(Environment $twig)
{
    echo $twig->render('levels.html.twig',array(
        // titulo, content, levels array
        'title' => 'Níveis',
        'levels' => array(
            ['levelName'=>"Iniciante",'description'=>'Lorem ipsum dolor sit amet.'],
            ['levelName'=>"Level 2",'description'=>'Consectetur adipiscing elit.']
        )
    ));
}

function trainingRoom(Environment $twig)
{
    echo $twig->render('trainingRoom.html.twig', array(
        ''
    ));
}

// Executa o sistema de roteamento
$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
dispatch($request, $twig);
