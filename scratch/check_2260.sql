SELECT os.idOs, os.contratos_id, u.nome FROM os JOIN usuarios u ON u.idUsuarios = os.usuarios_id WHERE idOs = 2260;
SELECT sc.sistemas_id, sc.local FROM sistemas_contratos sc WHERE contratos_id = (SELECT contratos_id FROM os WHERE idOs = 2260);
