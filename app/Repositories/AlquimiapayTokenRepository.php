<?php

namespace App\Repositories;


use App\Exceptions\CustomException;
use App\Models\AlquimiaLog;
use App\Models\AlquimiapayToken;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Session;

class AlquimiapayTokenRepository
{
    public function getWso2Token()
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $queryUrl = "token?grant_type=client_credentials";
        $url = $host . $queryUrl;


        $headers = [
            "Authorization: Basic R19GM1NTcFFyTTlYYTlEb29hVUdWOGpxbmJvYTp3Zmw3TDcycnk3dlBTc2NQcXdNMzVTMjhxaWth"
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($headers),
            'wso2_token' => 'wso2_token',
            'alquimia_token' => '',
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            $accessToken = $responseContentObj->access_token;

            return $accessToken;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function getAlquimiaToken()
    {
        $url = env('API_ALQUIMIA_TOKEN_URL');

        $data = [
            'grant_type' => 'password',
            'username' => 'crm@vixipay.com',
            'password' => 'VixiCards22*/',
            'client_id' => 'testclient',
            'client_secret' => 'testpass'
        ];

        $headers = [
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => '',
            'alquimia_token' => 'alquimia_token',
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            $accessToken = $responseContentObj->access_token;

            return $accessToken;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function obtenerCuentasAhorroCliente()
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/cuenta-ahorro-cliente";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => '',
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function obtenerTransacciones($idCuenta, $fecha_inicio = '', $fecha_fin = '')
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/cuenta-ahorro-cliente/$idCuenta/transaccion";

        $filterUrl = '?registros=1000000';
        if ($fecha_inicio) {
            $filterUrl = "&fecha_inicio=$fecha_inicio";
        }

        if ($fecha_fin) {
            $filterUrl = "&fecha_fin=$fecha_fin";
        }

        $url = $host . $alquimiaEnviroment . $queryUrl . $filterUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => '',
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function obtenerBalanceCuentaMadre($noTarjeta = '1000000130200675')
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "/1.0.0/v2/saldo-cuenta-ahorro?no_cuenta=$noTarjeta";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => '',
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        return $response;
    }

    public function obtenerBalance($noTarjeta)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "/1.0.0/v2/saldo-tarjeta-visa/$noTarjeta";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => '',
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function obtenerCuentasAhorropadre($idCuentaPadre = 28095)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "/1.0.0/v2/cuenta-ahorro-cliente?id_cuenta_ahorro_padre=$idCuentaPadre";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => '',
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function obtenerCuentasHijas($idCuentaPadre = 28095, $page = 1, $registros = 20, $sort = '-fecha_alta', &$total_count = 100000)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "/1.0.0/v2/cuenta-ahorro-cliente?id_cuenta_ahorro_padre=$idCuentaPadre&page=$page&registros=$registros&sort=$sort";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => '',
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();

            $responseHeaders = $response->headers;
            if (array_key_exists('x-pagination-total-count', $responseHeaders)) {
                $total_count = $responseHeaders['x-pagination-total-count'];
            }
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    /**
     * @param string $type 'wso2' or 'alquimia'
     * @param bool $model true(AlquimiapayToken Model), false(Token str)
     * @return Token str or AlquimiapayToken Model
     */
    public function getLastToken($type = 'wso2', $model = false)
    {
        $query = AlquimiapayToken::query();
        $query->orderBy('created_at', 'desc');
        if ($type == 'wso2') {
            $query->where('type', 'WSO2')->where('token', '!=', '0');
        } else {
            $query->where('type', 'ALQUIMIA')->where('token', '!=', '0');
        }

        $token = $query->first();

        if ($model) {
            return $token ? $token : null;
        } else {
            return $token ? $token->token : '';
        }
    }

    /**
     * @param int $idCuentaPadre
     * @return null
     */
    public function updateIdCuentas($fatherAccountId = 28095, $stopOnFirstAssociatedAccount = true)
    {
        $page = 1;
        $records = 20;
        $sort = '-fecha_alta';
        $firstAssociatedAccount = false;
        $total = 336;
        $count = 1;

        while (!$firstAssociatedAccount) {
            $alquimiaAccounts = $this->obtenerCuentasHijas($fatherAccountId, $page, $records, $sort, $total);
            $lengthResponse = count($alquimiaAccounts);

            if (!$alquimiaAccounts || $lengthResponse == 0) {
                throw new CustomException("Provider Error: ");
            } else {
                if ($lengthResponse != $records) {
                    $firstAssociatedAccount = true;
                }

                foreach ($alquimiaAccounts as $alquimiaAccount) {
                    $count++;
                    $account = Account::where('card_number', $alquimiaAccount->no_cuenta)->first();

                    if ($stopOnFirstAssociatedAccount && $account && $account->id_account != null) {
                        $firstAssociatedAccount = true;
                        break;
                    } elseif ($account && $account->id_account == null) {
                        $account->id_account = $alquimiaAccount->id_cuenta_ahorro;
                        $account->save();
                    }
                }

                if (!$firstAssociatedAccount) {
                    if ($count >= $total) {
                        $firstAssociatedAccount = true;
                    }
                    $page++;
                }
            }
        }

        return $alquimiaAccounts;
    }

    public function transaccionCuentaMadreATarjeta($importe, $cuenta_destino, $userId, $clientId, $fee, $feeId)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/guardar-transacciones";
        $url = $host . $alquimiaEnviroment . $queryUrl;
        $cuenta_origen = 28095;
        $id_cliente = 2620865;
        $medio_pago = 2;
        $time = time();
        $guarda_cuenta_destino = false;
        $nombre_beneficiario = "BRENDA GUADALUPE";
        $rfc_beneficiario = "VACB961109M79";
        $email_beneficiario = "crm@vixipay.com";
        $concepto = "CardLoad-$time";
        $no_referencia = $time;
        $api_key = "28b13c49202dd553238dd50e2581b009";

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $data = [
            'cuenta_origen' => $cuenta_origen,
            'id_cliente' => $id_cliente,
            'medio_pago' => $medio_pago,
            'importe' => $importe,
            'cuenta_destino' => $cuenta_destino,
            'guarda_cuenta_destino' => $guarda_cuenta_destino,
            'nombre_beneficiario' => $nombre_beneficiario,
            'rfc_beneficiario' => $rfc_beneficiario,
            'email_beneficiario' => $email_beneficiario,
            'concepto' => $concepto,
            'no_referencia' => $no_referencia,
            'api_key' => $api_key
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            $account = Account::where('card_number', $cuenta_destino)->first();
            $mytime = Carbon::now();

            Transaction::create([
                'date' => $mytime->toDateTimeString(),
                'amount' => $importe,
                'id_tx_alquimia' => $responseContentObj->id_transaccion,
                'state' => 'PENDING',
                'account_id' => $account->id,
                'client_id' => $clientId,
                'currency_id' => $account->currency_id,
                'fee_id' => $feeId,
                'transaction_type_id' => 1,
                'user_id' => $userId,
                'no_referencia_alquimia' => $time,
                'folio_orden_alquimia' => $responseContentObj->folio_orden,
                'send_amount_currency_id' => 4,
                'fee_amount' => $fee,
                'card_provider_id' => $account->card_provider_id,
            ]);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        return false;
    }

    public function transaccionCuentaClienteACliente($importe, $cuenta_destino, $userId, $clientId, $fee, $feeId, $request)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/guardar-transacciones";
        $url = $host . $alquimiaEnviroment . $queryUrl;
        $cuenta_origen = 17175;
        $id_cliente = 2620865;
        $medio_pago = 5;
        $time = time();
        $guarda_cuenta_destino = false;
        $nombre_beneficiario = "BRENDA GUADALUPE";
        $rfc_beneficiario = "VACB961109M79";
        $email_beneficiario = "crm@vixipay.com";
        $concepto = "TransferBetweenCards-$time";
        $no_referencia = $time;
        $api_key = "28b13c49202dd553238dd50e2581b009";

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $data = [
            'cuenta_origen' => $cuenta_origen,
            'id_cliente' => $id_cliente,
            'medio_pago' => $medio_pago,
            'importe' => $importe,
            'cuenta_destino' => $cuenta_destino,
            'guarda_cuenta_destino' => $guarda_cuenta_destino,
            'nombre_beneficiario' => $nombre_beneficiario,
            'rfc_beneficiario' => $rfc_beneficiario,
            'email_beneficiario' => $email_beneficiario,
            'concepto' => $concepto,
            'no_referencia' => $no_referencia,
            'api_key' => $api_key
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            $account = Account::where('card_number', $cuenta_destino)->first();
            $mytime = Carbon::now();

            Transaction::create([
                'date' => $mytime->toDateTimeString(),
                'amount' => $importe,
                'id_tx_alquimia' => $responseContentObj->id_transaccion,
                'state' => 'PENDING',
                'account_id' => $account->id,
                'client_id' => $clientId,
                'currency_id' => $account->currency_id,
                'fee_id' => $feeId,
                'transaction_type_id' => 1,
                'user_id' => $userId,
                'no_referencia_alquimia' => $time,
                'folio_orden_alquimia' => $responseContentObj->id_transaccion,
                'send_amount_currency_id' => 4,
                'fee_amount' => $fee,
                'user_json' => $this->getUserJson(),
                'card_provider_id' => $account->card_provider_id
            ]);

            return $responseContentObj;
        }
        $errorMessage = "Ocurrió un error con el proveedor al realizar la transacción.";

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            if ($responseContentObj->message == $errorMessage) {
                throw new CustomException("Provider Error: " . "The amount to transfer must be greater than ".$request->amount);
            } else {
                throw new CustomException("Provider Error: " . $responseContentObj->message);
            }
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        return false;
    }

    public function bloquearTarjeta($cuenta_destino)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/operaciones-tarjeta";
        $url = $host . $alquimiaEnviroment . $queryUrl;
        // $api_key = "28b13c49202dd553238dd50e2581b009";
        $api_key = "f86d2ef9e9b69d7b3685fe6d8f6447b0";
        $id_cliente = 2620865;
        // $id_cuenta_ahorro = 28095;
        $id_cuenta_ahorro = 31615;
        $operacion = 1;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $data = [
            'id_cliente' => $id_cliente,
            'id_cuenta_ahorro' => $id_cuenta_ahorro,
            'no_tarjeta' => $cuenta_destino,
            'operacion' => $operacion,
            'api_key' => $api_key
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        return false;
    }

    public function asignacionTarjetaCliente($cuenta_destino, $codigoActivacion)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/asignacion-tarjeta-cliente-codigo";
        $url = $host . $alquimiaEnviroment . $queryUrl;
        $alias = substr($cuenta_destino, -8);
        $id_cuenta_ahorro = 28095;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $data = [
            'id_cuenta_ahorro_padre' => $id_cuenta_ahorro,
            'no_tarjeta' => $cuenta_destino,
            'alias' => $alias,
            'codigo_activacion' => $codigoActivacion
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();
        
            $errorMessage = "El código de activación no coincide con el de la tarjeta.";

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            if ($responseContentObj->message == $errorMessage) {
                throw new CustomException("Provider Error: " . "The activation code does not match the one on the card.");
            } else {
                throw new CustomException("Provider Error: " . $responseContentObj->message);
            }
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function consultarTranferencia($id_transaccion, $id_cuenta)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/consulta-estatus-tx";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken"
        ];

        $data = [
            'id_transaccion' => $id_transaccion,
            'id_cuenta' => $id_cuenta,
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function consultarTranferenciasPendientes($id_cuenta)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/ordenes-importador";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken"
        ];

        $data = [
            'id_cuenta' => $id_cuenta,
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function autorizarTransaccionesPendientes($id_transaccion)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/ordenes-importador";
        $url = $host . $alquimiaEnviroment . $queryUrl;
        $id_cuenta = 28095;
        $accion = 1;
        $api_key = "28b13c49202dd553238dd50e2581b009";

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $data = [
            'id_transaccion' => $id_transaccion,
            'accion' => $accion,
            'id_cuenta' => $id_cuenta,
            'api_key' => $api_key
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }


        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function crearAPIKeyDinamicoPorCuenta($id_cuenta)
    {
        $host = env('API_ALQUIMIAPAY_URL');
        $alquimiaEnviroment = env('API_ALQUIMIAPAY_ENV');
        $queryUrl = "1.0.0/v2/cuenta-ahorro/30787/otp-dinamico";
        $url = $host . $alquimiaEnviroment . $queryUrl;

        $lastWso2Token = $this->getLastToken('wso2');
        $lastAlquimiaToken = $this->getLastToken('alquimia');

        $headers = [
            "Authorization: Bearer $lastWso2Token",
            "AuthorizationAlquimia: Bearer $lastAlquimiaToken",
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $ips = env('API_ALQUIMIA_SERVER_IP', '74.208.71.253');

        $uriWebhooksNotification = "api/v1/alquimia_webhooks/$id_cuenta";
        $url_notification = env('APP_URL', 'https://dapi.viximarkets.com') . $uriWebhooksNotification;
        $medios_pago = '1,2,3,4,5,6,7,8,9,10,11,12,13';
        $api_key = '28b13c49202dd553238dd50e2581b009';

        $data = [
            'ips' => $ips,
            'url_notification' => $url_notification,
            'medios_pago' => $medios_pago,
            'api_key' => $api_key
        ];

        $newAlquimiaLog = AlquimiaLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'wso2_token' => $lastWso2Token,
            'alquimia_token' => $lastAlquimiaToken,
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newAlquimiaLog->response = substr(json_encode($response), 0, 10240);
            $newAlquimiaLog->save();
        }

        if ($response->status == 200) {
            $responseContentObj = json_decode($response->content);

            return $responseContentObj;
        }

        if ($response->status == 422) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: " . $responseContentObj->message);
        }

        if ($response->status == 401) {
            $responseContentObj = json_decode($response->content);
            throw new CustomException("Provider Error: The supplier is not available at the moment");
        }

        throw new CustomException("Provider Error");
    }

    public function getUserJson()
    {
        $user = Session::get('user');

        return json_encode($user);
    }
}
