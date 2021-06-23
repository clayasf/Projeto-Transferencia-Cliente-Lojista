<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'usuarios'], function () use ($router)
{
    $router->get('/','UsuarioController@Usuarios') ;
    $router->get('/{id}','UsuarioController@Usuario') ;
    $router->post('/cadastrar', 'UsuarioController@cadastrarUsuario');
    $router->put('/atualizar/{id}', 'UsuarioController@atualizarUsuario');
    $router->delete('/deletar/{id}', 'UsuarioController@deletarUsuario');

});

$router->group(['prefix' => 'empresas'], function () use ($router)
{
    $router->get('/','EmpresaController@Empresas') ;
    $router->get('/{id}','EmpresaController@Empresa') ;
    $router->post('/cadastrar', 'EmpresaController@cadastrarEmpresa');
    $router->put('/atualizar/{id}', 'EmpresaController@atualizarEmpresa');
    $router->delete('/deletar/{id}', 'EmpresaController@deletarEmpresas');

});


$router->group(['prefix' => 'transferencia'], function () use ($router)
{
    $router->post('/empresa/deposito', 'transferenciaController@deposito');
    $router->post('/usuario/deposito', 'transferenciaController@deposito');
    $router->get('/', 'transferenciaController@transferencias');
    $router->get('/{id}','transferenciaController@transferencia') ;

});

$router->post('/login'  , 'AutorizacaoController@login'); 
$router->post('/logout' , 'AutorizacaoController@logout');
$router->post('/refresh', 'AutorizacaoController@refresh');
$router->post('/me'     , 'AutorizacaoController@me');