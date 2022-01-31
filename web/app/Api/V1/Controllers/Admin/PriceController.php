<?php

namespace App\Api\V1\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PriceController extends Controller
{
    /**
     * Getting a listing of product prices
     *
     * @OA\Get(
     *     path="/admin/prices",
     *     summary="Getting a listing of product prices",
     *     description="Getting a listing of product prices",
     *     tags={"Admin / Prices"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *     x={
     *         "auth-type": "Application & Application User",
     *         "throttling-tier": "Unlimited",
     *         "wso2-application-security": {
     *             "security-types": {"oauth2"},
     *             "optional": "false"
     *         }
     *     },
     *
     *     @OA\Response(
     *          response="200",
     *          description="Getting a listing of product prices"
     *     )
     * )
     *
     * @return mixed
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Price $price
     * @return Response
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Price $price
     * @return Response
     */
    public function update(Request $request, Price $price)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Price $price
     * @return Response
     */
    public function destroy(Price $price)
    {
        //
    }
}
