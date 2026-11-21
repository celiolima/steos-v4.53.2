-- Migration 024: Adequacao da tabela servicos_nfse ao Portal Nacional e Reforma Tributaria (IBS/CBS)
ALTER TABLE `servicos_nfse`
    ADD COLUMN IF NOT EXISTS `cst_ibs_cbs` VARCHAR(20) NULL DEFAULT '000' AFTER `aliquota`,
    ADD COLUMN IF NOT EXISTS `class_trib_ibs_cbs` VARCHAR(20) NULL DEFAULT '000001' AFTER `cst_ibs_cbs`,
    ADD COLUMN IF NOT EXISTS `ind_operacao` VARCHAR(20) NULL DEFAULT '' AFTER `class_trib_ibs_cbs`;

UPDATE `servicos_nfse`
SET
    `asaas_service_id` = NULL,
    `cst_ibs_cbs` = COALESCE(NULLIF(`cst_ibs_cbs`, ''), '000'),
    `class_trib_ibs_cbs` = COALESCE(NULLIF(`class_trib_ibs_cbs`, ''), '000001');

UPDATE `servicos_nfse`
SET `ind_operacao` = '010106', `aliquota` = 0.00
WHERE `codigo_servico_municipal` LIKE '%99.03%';

UPDATE `servicos_nfse`
SET `ind_operacao` = COALESCE(`ind_operacao`, ''), `aliquota` = 3.50
WHERE (`codigo_servico_municipal` LIKE '%14.01%' OR `codigo_servico_municipal` LIKE '%0101%')
  AND `codigo_servico_municipal` NOT LIKE '%99.03%';
