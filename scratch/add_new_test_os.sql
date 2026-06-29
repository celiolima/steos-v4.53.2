INSERT IGNORE INTO tecnicos_os (tecnico_id, os_id, tecnicoName) VALUES (1, 2270, 'Tiago');
INSERT IGNORE INTO tecnicos_os (tecnico_id, os_id, tecnicoName) VALUES (1, 2269, 'Tiago');
INSERT IGNORE INTO tecnicos_os (tecnico_id, os_id, tecnicoName) VALUES (1, 2268, 'Tiago');

INSERT INTO os (idOs, dataInicial, dataFinal, status, observacoes, clientes_id, usuarios_id, local, tipo, manutPreventiva, contratos_id, faturado, afaturar, dataAbertura) 
VALUES (2271, '2027-03-25 08:00:00', '2027-03-25 18:00:00', 'Aberto', 'manutenção preventiva teste AG', 1, 1, 'externo', 'contrato', 1, 1, 0, 0, NOW());

INSERT INTO os (idOs, dataInicial, dataFinal, status, observacoes, clientes_id, usuarios_id, local, tipo, manutPreventiva, contratos_id, faturado, afaturar, dataAbertura) 
VALUES (2272, '2027-04-25 08:00:00', '2027-04-25 18:00:00', 'Aberto', 'manutenção preventiva teste AG', 1, 1, 'externo', 'contrato', 1, 1, 0, 0, NOW());

INSERT INTO tecnicos_os (tecnico_id, os_id, tecnicoName) VALUES (1, 2271, 'Tiago');
INSERT INTO tecnicos_os (tecnico_id, os_id, tecnicoName) VALUES (1, 2272, 'Tiago');
