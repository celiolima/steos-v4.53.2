SELECT * FROM os_checklists;
SELECT checklist_id, count(*) FROM os_checklists_itens GROUP BY checklist_id;
