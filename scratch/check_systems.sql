SELECT idOs, contratos_id FROM os WHERE idOs = 2258;
SELECT * FROM sistemas_contratos WHERE contratos_id = (SELECT contratos_id FROM os WHERE idOs = 2258);
SELECT * FROM sistemas_checks LIMIT 10;
SELECT * FROM sistemas_contratos_checks LIMIT 10;
