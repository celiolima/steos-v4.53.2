-- Migration 025: Adiciona codigo_tributacao_nacional (cTribNac) e higieniza ind_operacao conforme API v3 do Asaas
ALTER TABLE `servicos_nfse`
    ADD COLUMN `codigo_tributacao_nacional` VARCHAR(20) NULL DEFAULT '' AFTER `codigo_nbs`;

UPDATE `servicos_nfse`
SET
    `asaas_service_id` = NULL,
    `cst_ibs_cbs` = COALESCE(NULLIF(`cst_ibs_cbs`, ''), '000'),
    `class_trib_ibs_cbs` = COALESCE(NULLIF(`class_trib_ibs_cbs`, ''), '000001');

UPDATE `servicos_nfse`
SET `codigo_tributacao_nacional` = '140101', `ind_operacao` = '', `aliquota` = 3.50
WHERE `codigo_servico_municipal` LIKE '%14.01%' AND `codigo_servico_municipal` NOT LIKE '%99.03%';

UPDATE `servicos_nfse`
SET `codigo_tributacao_nacional` = '010101', `ind_operacao` = '', `aliquota` = 3.50
WHERE (`codigo_servico_municipal` LIKE '%0101%' OR `codigo_servico_municipal` LIKE '%01.01%')
  AND `codigo_servico_municipal` NOT LIKE '%99.03%'
  AND `codigo_servico_municipal` NOT LIKE '%14.01%';

UPDATE `servicos_nfse`
SET `codigo_tributacao_nacional` = '', `ind_operacao` = '010106', `aliquota` = 0.00
WHERE `codigo_servico_municipal` LIKE '%99.03%';

UPDATE `servicos_nfse`
SET `ind_operacao` = ''
WHERE `ind_operacao` IS NOT NULL
  AND `ind_operacao` != ''
  AND `ind_operacao` NOT IN (
      '010101','010102','010103','010104','010105','010106','010201',
      '020101','020201','020301','030101','030102','030103','030104',
      '040101','050101','050102','050103','050104','050201','060101',
      '070101','070102','080101','100101','100102','100201','100301',
      '100302','100401','100501','100502','100601'
  );
