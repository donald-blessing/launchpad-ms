<?php

namespace App\Api\V1\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TokenRewardResource;
use App\Models\TokenReward;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenRewardController extends Controller
{
    /**
     * Method for list of user's tokenReward.
     *
     * @OA\Get(
     *     path="/admin/token-rewards",
     *     description="Get list of un-approved user's tokenReward",
     *     tags={"Admin / TokenRewards"},
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
     *     @OA\Parameter(
     *         name="limit",
     *         description="Count of token rewards in one page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *              default=20
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     *
     * Method for list of token Reward of users.
     *
     * @param Request $request
     *
     * @return
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        try {
            $result = TokenReward::paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi($result->toArray());
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Token rewards list',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Method for show user's token reward
     *
     * @OA\Get(
     *     path="/admin/token-rewards/{token_reward_id}",
     *     description="Get tokenReward of user by token_reward_id",
     *     tags={"Admin / TokenRewards"},
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
     *     @OA\Parameter(
     *         name="token_reward_id",
     *         description="TokenReward ID",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     *
     * @param         $token_reward_id
     *
     * @return JsonResponse
     */
    public function show($token_reward_id): JsonResponse
    {
        try {
            $tokenReward = TokenReward::find($token_reward_id);

            if (!$tokenReward) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tokenReward of user with id=' . $token_reward_id,
                ], 400);
            }

            return response()->json([
                'success' => true,
                'tokenReward' => new TokenRewardResource ($tokenReward),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Method for storage of user's token Reward.
     *
     * @OA\Post(
     *     path="/admin/token-rewards/store",
     *     description="Store user's tokenReward",
     *     tags={"Admin / TokenRewards"},
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
     *     @OA\Parameter(
     *         name="purchase_band",
     *         description="Serial number of purchase",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="swap",
     *         description="Number of tokens",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deposit_amount",
     *         description="Amount money to be depoosited",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="reward_bonus",
     *         description="Reward bonus for token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     *
     * Method for storage of token Reward of users.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Request $request): JsonResponse
    {
        $tokenReward = null;
        try {
            DB::transaction(function () use ($request, &$tokenReward) {
                $tokenReward = TokenReward::create($request->all());
            });
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
        return response()->json(['success' => true], 200);
    }

    /**
     * Method for storage of user's token Reward.
     *
     * @OA\Putt(
     *     path="/admin/token-rewards/store",
     *     description="Store user's tokenReward",
     *     tags={"Admin / TokenRewards"},
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
     *     @OA\Parameter(
     *         name="purchase_band",
     *         description="Serial number of purchase",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="swap",
     *         description="Number of tokens",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deposit_amount",
     *         description="Amount money to be depoosited",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="reward_bonus",
     *         description="Reward bonus for token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *              default=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     *
     * Method for update of token Reward of users.
     *
     * @param Request     $request
     * @param TokenReward $tokenReward
     *
     * @return JsonResponse
     *
     */
    public function update(Request $request, TokenReward $tokenReward): JsonResponse
    {
        try {
            DB::transaction(function () use ($request, &$tokenReward) {
                $tokenReward->update($request->all());
            });

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
        return response()->json(['success' => true], 200);
    }

    /**
     * Method for delete tokenReward by token_reward_id
     *
     * @OA\Delete(
     *     path="/admin/token-rewards/{token_reward_id}",
     *     description="destroy user's token rewards by token_reward_id",
     *     tags={"Admin / TokenRewards"},
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
     *     @OA\Parameter(
     *         name="token_reward_id",
     *         description="TokenReward ID",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     *
     * @param $token_reward_id
     *
     * @return JsonResponse
     */
    public function destroy($token_reward_id): JsonResponse
    {
        try {
            $tokenReward = TokenReward::find($token_reward_id);
            if (!$tokenReward)
                return response()->json([
                    'success' => false,
                    'error' => 'No token reward  with id=' . $token_reward_id,
                ], 400);


            $tokenReward->delete();

            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
