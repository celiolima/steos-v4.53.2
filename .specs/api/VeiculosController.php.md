# Documentação: VeiculosController.php

## 1. Identificação
- **Caminho:** `e:\DEV\EMISSOR DE NOTAS\STEOS\application\controllers\api\v1\VeiculosController.php`
- **Tipo:** REST Controller (`REST_Controller`)
- **Módulo:** `api`

## 2. Propósito
Consulta da frota de veículos (`veiculos`), histórico de odômetro/abastecimento (`gasolina`) e auditoria (`veiculos_logs`) mediante permissão `vVeiculo`.

## 3. Endpoints
- `GET /api/v1/veiculos`: lista paginada de veículos cadastrados.
- `GET /api/v1/veiculos/{id}`: dados completos do veículo com `abastecimentos` (`Gasolina_model::getByIdEx`) e últimos 25 registros de `veiculos_logs`.
