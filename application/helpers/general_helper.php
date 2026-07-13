<?php

use Piggly\Pix\Parser;

if (! function_exists('convertUrlToUploadsPath')) {
    function convertUrlToUploadsPath($url)
    {
        if (! $url) {
            return;
        }

        return FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . basename($url);
    }
}

if (! function_exists('limitarTexto')) {
    function limitarTexto($texto, $limite)
    {
        $contador = strlen($texto);

        if ($contador >= $limite) {
            $texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';

            return $texto;
        } else {
            return $texto;
        }
    }
}

if (! function_exists('getMoneyAsCents')) {
    function getMoneyAsCents($value)
    {
        // make sure we are dealing with a proper number now, no +.4393 or 3...304 or 76.5895,94
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException('A entrada deve ser numérica!');
        }

        return intval(round(floatval($value), 2) * 100);
    }
}

if (! function_exists('getCobrancaTransactionStatus')) {
    function getCobrancaTransactionStatus($paymentGatewaysConfig, $paymentGateway, $status)
    {
        return @$paymentGatewaysConfig[$paymentGateway]['transaction_status'][$status] ?: $status;
    }
}

if (! function_exists('getCobrancaStatusBadge')) {
    function getCobrancaStatusBadge($status, $gateways_config = null, $payment_gateway = null)
    {
        $status_cob_text = $status;
        if (function_exists('getCobrancaTransactionStatus') && !empty($gateways_config) && !empty($payment_gateway)) {
            $status_cob_text = getCobrancaTransactionStatus($gateways_config, $payment_gateway, $status);
        }

        $status_badge = '';
        switch ($status) {
            case 'PENDING':
            case 'waiting':
                $status_badge = '<span class="label label-warning" title="' . htmlspecialchars($status_cob_text) . '">Aguardando Pagamento</span>';
                break;
            case 'RECEIVED':
            case 'paid':
            case 'RECEIVED_IN_CASH':
                $status_badge = '<span class="label label-success" style="background-color: #353535;" title="' . htmlspecialchars($status_cob_text) . '">Recebida (Paga)</span>';
                break;
            case 'CONFIRMED':
            case 'identified':
                $status_badge = '<span class="label label-success" title="' . htmlspecialchars($status_cob_text) . '">Confirmada</span>';
                break;
            case 'OVERDUE':
            case 'expired':
                $status_badge = '<span class="label label-important" title="' . htmlspecialchars($status_cob_text) . '">Vencida</span>';
                break;
            case 'DELETED':
            case 'canceled':
                $status_badge = '<span class="label label-inverse" title="' . htmlspecialchars($status_cob_text) . '">Cancelada / Excluída</span>';
                break;
            case 'REFUNDED':
            case 'refunded':
                $status_badge = '<span class="label label-info" title="' . htmlspecialchars($status_cob_text) . '">Estornada</span>';
                break;
            default:
                $status_badge = '<span class="label" title="' . htmlspecialchars($status_cob_text) . '">' . htmlspecialchars($status_cob_text ?: $status) . '</span>';
                break;
        }

        return $status_badge;
    }
}

if (! function_exists('getPixKeyType')) {
    function getPixKeyType($value)
    {
        if (Parser::validateDocument($value)) {
            return Parser::KEY_TYPE_DOCUMENT;
        }

        if (Parser::validateEmail($value)) {
            return Parser::KEY_TYPE_EMAIL;
        }

        if (Parser::validatePhone($value)) {
            return Parser::KEY_TYPE_PHONE;
        }

        if (Parser::validateRandom($value)) {
            return Parser::KEY_TYPE_RANDOM;
        }

        return null;
    }
}

if (! function_exists('getAmount')) {
    function getAmount($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot);

        return floatval(str_replace(',', '.', $removedThousandSeparator));
    }
}

if (! function_exists('json_decode_legacy')) {
    function json_decode_legacy(string $raw): mixed
    {
        $decoded = json_decode($raw, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            $decoded = unserialize($raw, ['allowed_classes' => false]);
        }

        return $decoded;
    }
}

if (! function_exists('printSafeHtml')) {
    function printSafeHtml(string $html): string
    {
        static $purifier = null;

        if ($purifier === null) {
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
        }

        return $purifier->purify($html);
    }
}
