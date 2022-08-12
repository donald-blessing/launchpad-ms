<?php

namespace App\Api\V1\Controllers\Application;

use App\Api\V1\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Traits\CryptoConversionTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    use CryptoConversionTrait;

    /**
     * Display list of all purchase - shopping List
     *
     * @OA\Get(
     *     path="/app/purchases",
     *     description="Getting list of all purchases tokens - shopping list",
     *     tags={"Application | Purchases"},
     *
     *     security={{
     *         "bearerAuth": {},
     *         "apiKey": {}
     *     }},
     *
     *     @OA\Parameter(
     *         name="limit",
     *         description="Count of purchase in response",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         description="Page of list",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1,
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Getting product list for start presale",
     *         @OA\JsonContent(ref="#/components/schemas/OkResponse")
     *     ),
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): mixed
    {
        try {
            $allPurchase = Purchase::orderBy('created_at', 'Desc')
                ->with(['product' => function ($query) {
                    $query->select('title', 'ticker', 'supply', 'presale_percentage', 'start_date', 'end_date', 'icon');
                }])
                ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'title' => 'List all purchase',
                'message' => 'List all purchase',
                'data' => $allPurchase
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'List all purchase',
                'message' => 'Error in getting list of all purchase: ' . $e->getMessage(),
            ], $e->getCode());
        }
    }

    /**
     * Purchase a Token
     *
     * @OA\Post(
     *     path="/app/purchases",
     *     summary="Purchase a token",
     *     description="Create a token purchase order. Currency ticker should be btc, eth, usd, gbp or eur. Only currency type of fiat and crypto is required",
     *     tags={"Application | Purchases"},
     *
     *     security={{
     *         "bearerAuth": {},
     *         "apiKey": {}
     *     }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Purchase")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/OkResponse")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/OkResponse")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DangerResponse")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/WarningResponse")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation Failed",
     *         @OA\JsonContent(ref="#/components/schemas/WarningResponse")
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Unknown error"
     *     )
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        // Try to save purchased token data
        try {
            $result = $this->handle($request);

            // Create new token purchase order
            $purchase = Purchase::create([
                'product_id' => $request->get('product_id'),
                'user_id' => auth()->user()->getAuthIdentifier(),
                'payment_amount' => $result['payment_amount'],
                'currency_ticker' => $request->get('currency_ticker'),
                'currency_type' => $result['currency_type'],
                "token_amount" => $result['token']['amount'],
                "bonus" => $result['token']['bonus'],
                "total_token" => $result['token']['total'],
            ]);

            // Return response to client
            return response()->jsonApi([
                'title' => 'Creating new token purchase order',
                'message' => "New purchase order has been created successfully",
                'data' => [
                    'amount' => $purchase->payment_amount,
                    'currency' => $purchase->currency_ticker,
                    'document' => [
                        'id' => $purchase->id,
                        'object' => class_basename(get_class($purchase)),
                        'service' => env('RABBITMQ_EXCHANGE_NAME'),
                        'meta' => $purchase
                    ]]
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'Creating new token purchase order',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get token worth
     *
     * @OA\Post(
     *     path="/app/purchases/token-worth",
     *     summary="Get token worth",
     *     description="Get token worth",
     *     description="Get token worth. Currency ticker should be btc, eth, usd, gbp or eur. Only currency type of fiat and crypto is required",
     *     tags={"Application | Purchases"},
     *
     *     security={{
     *         "bearerAuth": {},
     *         "apiKey": {}
     *     }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Purchase")
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="ok",
     *         @OA\JsonContent(ref="#/components/schemas/OkResponse")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/OkResponse")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error",
     *         @OA\JsonContent(ref="#/components/schemas/DangerResponse")
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/WarningResponse")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation Failed",
     *         @OA\JsonContent(ref="#/components/schemas/WarningResponse")
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Unknown error"
     *     )
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function tokenWorth(Request $request): mixed
    {
        // Try to save purchased token data
        try {
            $result = $this->handle($request);

            // Return response to client
            return response()->jsonApi([
                'title' => 'Get token worth',
                'message' => "Get token worth",
                'data' => [
                    "currency_ticker" => $request->currency_ticker,
                    "rate" => $result['rate'],
                    "payment_amount" => $result['payment_amount'],
                    "token_amount" => $result['token']['amount'],
                    "bonus" => $result['token']['bonus'],
                    "total_token" => $result['token']['total'],
                ]
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'Get token worth',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @param $request
     * @return array
     * @throws Exception
     */
    private function handle($request): mixed
    {
        $rules = [
            'product_id' => 'required|string',
            'currency_ticker' => 'required|string'
        ];

        // Convert currency
        $currency = strtolower($request->get('currency_ticker'));
        if (in_array($currency, ['usd', 'eur', 'gbp'])) {
            $result['currency_type'] = 'fiat';

            $rules += [
                'payment_amount' => 'required|numeric|min:250|max:1000',
            ];
        } else {
            $result['currency_type'] = 'crypto';

            $rules += [
                'payment_amount' => 'required|numeric',
            ];
        }

        // Do validate input data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException('Validation error occurred!', 422);
        }

        // get product details
        $product = Product::find($request->get('product_id'));
        if (!$product) {
            throw new Exception('Product not found', 400);
        }

        // get rate of token
        $result['rate'] = $this->getTokenExchangeRate('usd', $currency);

        // Get payment_amount
        $result['payment_amount'] = $result['rate'] * $request->payment_amount;

        // Get calculated token worth
        $result['token'] = $this->getTokenWorth($request->payment_amount, $product->ticker);

        // return response
        return $result;
    }
}
