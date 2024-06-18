<?php

namespace Xentixar\EsewaSdk;

class Esewa
{
    protected float $amount;
    protected float $tax_amount;
    protected float $total_amount;
    protected string $transaction_uuid;
    protected string $product_code;
    protected float $product_service_charge;
    protected float $product_delivery_charge;
    protected string $success_url;
    protected string $failure_url;
    protected string $signed_field_names;
    protected string $signature;
    protected string $secret_key;

    public function config(string $success_url, string $failure_url, float $amount, string $product_code = 'EPAYTEST', string $secret_key = '8gBm/:&EnhH.1/q', float $tax_amount = 0, float $product_service_charge = 0, float $product_delivery_charge = 0)
    {
        $this->success_url = $success_url;
        $this->failure_url = $failure_url;
        $this->amount = $amount;
        $this->tax_amount = $tax_amount;
        $this->product_code = $product_code;
        $this->product_service_charge = $product_service_charge;
        $this->product_delivery_charge = $product_delivery_charge;
        $this->signed_field_names = "total_amount,transaction_uuid,product_code";
        $this->total_amount = $this->tax_amount + $this->amount + $this->product_delivery_charge + $this->product_service_charge;
        $this->secret_key = $secret_key;
        $this->transaction_uuid = uniqid() . time();
        $this->signature = $this->generateHmacSignature($this->total_amount, $this->transaction_uuid, $this->product_code, $this->secret_key);
    }

    public function init(bool $production = false)
    {
        $postData = [
            "amount" => $this->amount,
            "failure_url" => $this->failure_url,
            "product_delivery_charge" => $this->product_delivery_charge,
            "product_service_charge" => $this->product_service_charge,
            "product_code" => $this->product_code,
            "signature" => $this->signature,
            "signed_field_names" => "total_amount,transaction_uuid,product_code",
            "success_url" => $this->success_url,
            "tax_amount" => $this->tax_amount,
            "total_amount" => $this->total_amount,
            "transaction_uuid" => $this->transaction_uuid
        ];

        if ($production) {
            $url = "https://epay.esewa.com.np/api/epay/main/v2/form";
        } else {
            $url = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
        }

        echo "<form id='esewaForm' action='$url' method='post'>";
        foreach ($postData as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        echo '</form>';
        echo '<script type="text/javascript">document.getElementById("esewaForm").submit();</script>';
    }

    public function decode()
    {
        if (isset($_GET['data'])) {
            $data = $_GET['data'];
            $jsonString =  base64_decode($data);
            $dataArray = json_decode($jsonString, true);
            return $dataArray;
        }
    }

    public function validate(string $total_amount, string $transaction_uuid, bool $production = false, string $product_code = 'EPAYTEST')
    {
        if (!$production) {
            $url = "https://uat.esewa.com.np/api/epay/transaction/status/?product_code=$product_code&total_amount=$total_amount&transaction_uuid=$transaction_uuid";
        } else {
            $url = "https://epay.esewa.com.np/api/epay/transaction/status/?product_code=$product_code&total_amount=$total_amount&transaction_uuid=$transaction_uuid";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!$production) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        $response = curl_exec($ch);
        return $response;
    }

    protected function generateHmacSignature($total_amount, $transaction_uuid, $product_code, $secret_key): string
    {
        $data = "total_amount=" . $total_amount . ",transaction_uuid=" . $transaction_uuid . ",product_code=" . $product_code;
        $signature = hash_hmac('sha256', $data, $secret_key, true);
        $base64Signature = base64_encode($signature);
        return $base64Signature;
    }
}
