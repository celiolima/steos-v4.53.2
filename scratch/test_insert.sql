INSERT INTO os_checklists_itens (checklist_id, sistema, local, check_desc, status, obs_local, os_local, descricao)
VALUES (3, 'TESTE SIS', 'TESTE LOC', 'TESTE CHECK', 'V', 'OBS', 'OS', 'TESTE CHECK');
SELECT * FROM os_checklists_itens WHERE checklist_id = 3;
