docker exec -i steos_mysql mysql -u steos -psteos -e "DROP DATABASE steos; CREATE DATABASE steos;" -- apaga tudo banco e tabelas e cria de novo
docker restart steos -- reinicia o container
docker exec -i steos_mysql mysql -u steos -psteos -e "DROP DATABASE steos; CREATE DATABASE steos;"-- cria banco
docker exec -i steos_mysql mysql -u steos -psteos -N -e "SELECT CONCAT('TRUNCATE TABLE ', table_name, ';') FROM information_schema.tables WHERE table_schema = 'steos';" > limpar_tudo.sql-- apaga todas as tabelas
Get-Content "e:\DEV\EMISSOR DE NOTAS\STEOS\stesiste_steos.sql\dados_steos.sql" | docker exec -i steos_mysql mysql -u steos -psteos steos -- importa os dados
