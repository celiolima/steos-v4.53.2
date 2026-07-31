<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Simulador_asaas extends MY_Controller
{
    public function index()
    {
        $tipoEvento = $this->input->get('evento');
        $httpCode = null;
        $response = null;
        $mensagemSimulador = "Escolha uma das opções abaixo para simular um envio do Asaas.";

        if ($tipoEvento === 'INVOICE_AUTHORIZED') {
            // Pegar a última O.S que tem um payment_id para amarrar a nota nela
            $os_com_payment = $this->db->where('asaas_payment_id !=', '')->where('asaas_payment_id IS NOT NULL', null, false)->order_by('idOs', 'DESC')->get('os')->row();
            $paymentId = $os_com_payment ? $os_com_payment->asaas_payment_id : 'pay_1234567890';
            $osIdString = $os_com_payment ? $os_com_payment->idOs : 'N/A';
            
            $payload = [
                "event" => "INVOICE_AUTHORIZED",
                "invoice" => [
                    "id" => "inv_" . rand(1000000000, 9999999999),
                    "payment" => $paymentId,
                    "status" => "AUTHORIZED",
                    "number" => (string)rand(1000, 9999),
                    "pdfUrl" => "https://sandbox.asaas.com/invoice/pdf/12345",
                    "xmlUrl" => "https://sandbox.asaas.com/invoice/xml/12345"
                ]
            ];
            $mensagemSimulador = "Enviamos um evento <b>INVOICE_AUTHORIZED</b> fictício amarrado à cobrança <b>{$paymentId}</b> (OS {$osIdString}).";
        } elseif ($tipoEvento === 'PAYMENT_CREATED') {
            $payload = [
                "event" => "PAYMENT_CREATED",
                "payment" => [
                    "id" => "pay_" . rand(1000000000, 9999999999),
                    "customer" => "cus_000005114757",
                    "subscription" => "sub_ag0j47f3m88tk4k7",
                    "value" => 200.00,
                    "dueDate" => date('Y-m-d', strtotime('+10 days')),
                    "billingType" => "BOLETO",
                    "invoiceUrl" => "https://sandbox.asaas.com/i/12345"
                ]
            ];
            $mensagemSimulador = "Enviamos um evento <b>PAYMENT_CREATED</b> fictício para a assinatura real <b>sub_ag0j47f3m88tk4k7</b> (Contrato #23).";
        }

        if ($tipoEvento) {
            $ch = curl_init(site_url('asaas_webhook/receber'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            // Passando o header com o token de segurança que está no seu .env_pro
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'asaas-access-token: whsec_0o2iYIrD2nbhJb7tgKj5KVEuP6uoeKWVpm-POLshLKE'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        }

        echo "<div style='font-family: sans-serif; padding: 20px;'>";
        echo "<h1>Simulador de Webhook do Asaas</h1>";
        
        echo "<div style='margin-bottom: 20px;'>";
        echo "<a href='?evento=PAYMENT_CREATED' style='padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Simular Nova Cobrança</a>";
        echo "<a href='?evento=INVOICE_AUTHORIZED' style='padding: 10px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px;'>Simular Emissão de NFS-e</a>";
        echo "</div>";

        echo "<p>{$mensagemSimulador}</p>";
        
        if ($tipoEvento) {
            if ($httpCode == 200) {
                echo "<h3 style='color: green;'>Status HTTP: {$httpCode} - Sucesso!</h3>";
            } elseif ($httpCode == 500) {
                echo "<h3 style='color: orange;'>Status HTTP: {$httpCode} - Erro forçado proposital (Retry)</h3>";
            } else {
                echo "<h3 style='color: red;'>Status HTTP: {$httpCode}</h3>";
            }

            echo "<h4>Resposta do sistema local:</h4>";
            echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>" . htmlspecialchars($response) . "</pre>";
        }
        
        echo "<br><br>";
        echo "<a href='" . site_url('financeiro/logsAsaas') . "' style='padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;'>Ir para tela de Logs Asaas</a>";
        echo "</div>";
    }
}
