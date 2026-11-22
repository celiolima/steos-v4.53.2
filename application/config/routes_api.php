<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Rotas API V1
$route['api/v1'] = 'api/v1/ApiController/index';
$route['api/v1/audit'] = 'api/v1/ApiController/audit';
$route['api/v1/emitente'] = 'api/v1/ApiController/emitente';
$route['api/v1/calendario'] = 'api/v1/ApiController/calendario';
$route['api/v1/login'] = 'api/v1/UsuariosController/login';
$route['api/v1/reGenToken'] = 'api/v1/UsuariosController/reGenToken';
$route['api/v1/conta'] = 'api/v1/UsuariosController/conta';
$route['api/v1/clientes'] = 'api/v1/ClientesController/index';
$route['api/v1/clientes/(:num)'] = 'api/v1/ClientesController/index/$1';
$route['api/v1/produtos'] = 'api/v1/ProdutosController/index';
$route['api/v1/produtos/(:num)'] = 'api/v1/ProdutosController/index/$1';
$route['api/v1/servicos'] = 'api/v1/ServicosController/index';
$route['api/v1/servicos/(:num)'] = 'api/v1/ServicosController/index/$1';
$route['api/v1/usuarios'] = 'api/v1/UsuariosController/index';
$route['api/v1/usuarios/(:num)'] = 'api/v1/UsuariosController/index/$1';
$route['api/v1/os'] = 'api/v1/OsController/index';
$route['api/v1/os/(:num)'] = 'api/v1/OsController/index/$1';
$route['api/v1/os/(:num)/produtos'] = 'api/v1/OsController/produtos/$1';
$route['api/v1/os/(:num)/produtos/(:num)'] = 'api/v1/OsController/produtos/$1/$2';
$route['api/v1/os/(:num)/servicos'] = 'api/v1/OsController/servicos/$1';
$route['api/v1/os/(:num)/servicos/(:num)'] = 'api/v1/OsController/servicos/$1/$2';
$route['api/v1/os/(:num)/anotacoes'] = 'api/v1/OsController/anotacoes/$1';
$route['api/v1/os/(:num)/anotacoes/(:num)'] = 'api/v1/OsController/anotacoes/$1/$2';
$route['api/v1/os/(:num)/anexos'] = 'api/v1/OsController/anexos/$1';
$route['api/v1/os/(:num)/anexos/(:num)'] = 'api/v1/OsController/anexos/$1/$2';
$route['api/v1/os/(:num)/desconto'] = 'api/v1/OsController/desconto/$1';
$route['api/v1/os/(:num)/checklists'] = 'api/v1/OsController/checklists/$1';

// Novos modulos administrativos STEOS v4.53.2
$route['api/v1/contratos'] = 'api/v1/ContratosController/index';
$route['api/v1/contratos/(:num)'] = 'api/v1/ContratosController/index/$1';

$route['api/v1/cobrancas'] = 'api/v1/CobrancasController/index';
$route['api/v1/cobrancas/(:num)'] = 'api/v1/CobrancasController/index/$1';
$route['api/v1/cobrancas/(:num)/pix'] = 'api/v1/CobrancasController/pix/$1';

$route['api/v1/nfse'] = 'api/v1/NfseController/index';
$route['api/v1/nfse/servicos'] = 'api/v1/NfseController/servicos';
$route['api/v1/nfse/emitir/(:num)'] = 'api/v1/NfseController/emitir/$1';
$route['api/v1/nfse/consultar/(:num)'] = 'api/v1/NfseController/consultar/$1';
$route['api/v1/nfse/cancelar/(:num)'] = 'api/v1/NfseController/cancelar/$1';
$route['api/v1/nfse/(:num)'] = 'api/v1/NfseController/index/$1';

$route['api/v1/financeiro'] = 'api/v1/FinanceiroController/index';
$route['api/v1/financeiro/resumo'] = 'api/v1/FinanceiroController/resumo';
$route['api/v1/financeiro/(:num)'] = 'api/v1/FinanceiroController/index/$1';

$route['api/v1/veiculos'] = 'api/v1/VeiculosController/index';
$route['api/v1/veiculos/(:num)'] = 'api/v1/VeiculosController/index/$1';

$route['api/v1/vendas'] = 'api/v1/VendasController/index';
$route['api/v1/vendas/(:num)'] = 'api/v1/VendasController/index/$1';

/*
Routes for clients API
Rotas Para API area do cliente.
*/

$route['api/v1/client'] = 'api/v1/client/ClientOsController/index';
$route['api/v1/client/auth'] = 'api/v1/client/ClientLoginController/login';
$route['api/v1/client/auth-bot'] = 'api/v1/client/ClientLoginController/auth_bot';

$route['api/v1/client/os'] = 'api/v1/client/ClientOsController/os';
$route['api/v1/client/os/(:num)'] = 'api/v1/client/ClientOsController/os/$1';

$route['api/v1/client/compras'] = 'api/v1/client/ClientComprasController/index';
$route['api/v1/client/compras/(:num)'] = 'api/v1/client/ClientComprasController/index/$1';

$route['api/v1/client/cobrancas'] = 'api/v1/client/ClientCobrancasController/index';

$route['api/v1/client/contratos'] = 'api/v1/client/ClientContratosController/index';
$route['api/v1/client/contratos/(:num)'] = 'api/v1/client/ClientContratosController/index/$1';
