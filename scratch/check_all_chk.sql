SELECT * FROM os_checklists;
SELECT checklist_id, COUNT(*) as qtd FROM os_checklists_itens GROUP BY checklist_id;
