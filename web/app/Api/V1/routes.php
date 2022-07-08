<?php

/**
 * @var Laravel\Lumen\Routing\Router $router
 */
$router->group([
    'prefix' => env('APP_API_VERSION', ''),
    'namespace' => '\App\Api\V1\Controllers'
], function ($router) {
    /**
     * PUBLIC ACCESS
     */
    $router->group([
        'namespace' => 'Public'
    ], function ($router) {
        /**
         * Products for public access
         */
        $router->group([
            'prefix' => 'products',
        ], function ($router) {
            $router->get('/', 'ProductController@index');
            $router->get('/{id}', 'ProductController@show');
        });

        $router->get('/token-rewards', 'TokenRewardController@index');
    });

    /**
     * USER APPLICATION PRIVATE ACCESS
     */
    $router->group([
        'middleware' => 'checkUser',
        'namespace' => 'Application'
    ], function ($router) {
        /**
         * Token Rewards
         */
        // $router->group([
        //     'prefix' => 'token-rewards',
        // ], function ($router) {
        //     $router->get('/', 'TokenRewardController@index');
        //     $router->post('/', 'TokenRewardController@store');
        //     $router->put('/', 'TokenRewardController@update');
        //     $router->delete('/', 'TokenRewardController@destroy');
        // });

        /**
         * Prices
         */
        $router->group([
            'prefix' => 'prices',
        ], function ($router) {
            $router->get('/', 'PriceController');
            $router->get('/{stage}', 'PriceController@getPriceByStage');
        });

        /**
         * Init first Investment (registration)
         */
        $router->group([
            'prefix' => 'investment',
            'namespace' => 'Application'
        ], function ($router) {
            $router->post('/', 'InvestmentController');
        });

        /**
         * Orders
         */
        $router->group([
            'prefix' => 'orders',
        ], function ($router) {
            $router->get('/', 'OrderController@index');
            $router->get('/{id}', 'OrderController@show');
            $router->post('/', 'OrderController@store');
        });

        /**
         * Deposits
         */
        $router->group([
            'prefix' => 'deposits',
        ], function ($router) {
            $router->get('/', 'DepositController@index');
            $router->get('/{id}', 'DepositController@show');
            $router->post('/', 'DepositController@store');
            $router->get('/get-pdf/{transaction_id}', 'DepositController@generatePdfForTransaction');
        });

        /**
         * Token Purchase
         *
         */
        $router->post('/purchase-token', 'PurchaseController@store');

        /**
         * Token Sales Progress
         */
        $router->get('/token-sales-progress', 'DashboardController@tokenSalesProgress');
    });

    /**
     * ADMIN PANEL ACCESS
     */
    $router->group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => [
            'checkUser',
            'checkAdmin'
        ]
    ], function ($router) {

        /**
         * Products
         */
        $router->group(['prefix' => 'products'], function ($router) {
            $router->get('/', 'ProductController@index');
            $router->post('/', 'ProductController@store');
            $router->get('/{id:[a-fA-F0-9\-]{36}}', 'ProductController@show');
            $router->put('/{id:[a-fA-F0-9\-]{36}}', 'ProductController@update');
            $router->delete('/{id:[a-fA-F0-9\-]{36}}', 'ProductController@destroy');
        });

        /**
         * Price
         * 
         */
        $router->group(['prefix' => 'price'], function ($router) {
            $router->get('/', 'PriceController@index');
            $router->post('/', 'PriceController@store');
            $router->get('/{id}', 'PriceController@show');
            $router->put('/{id}', 'PriceController@update');
            $router->delete('/{id}', 'PriceController@destroy');
        });

        /**
         * Token Rewards
         */
        $router->group([
            'prefix' => 'token-rewards',
        ], function ($router) {
            $router->get('/', 'TokenRewardController@index');
            $router->post('/', 'TokenRewardController@store');
            $router->get('/{token_reward_id}', 'TokenRewardController@show');
            $router->put('/{token_reward_id}', 'TokenRewardController@update');
            $router->delete('/{token_reward_id}', 'TokenRewardController@destroy');
        });

        /**
         * Transactions
         */
        $router->group([
            'prefix' => 'transactions',
        ], function ($router) {
            $router->get('/', 'TransactionController');
            $router->post('/', 'TransactionController@store');
        });

        /**
         * Admin/Deposit
         */
        $router->group([
            'prefix' => 'deposits',
        ], function ($router) {
            $router->get('/',       'DepositController@index');
            $router->post('/',      'DepositController@store');

            $router->get('{id}',    'DepositController@show');
            $router->put('{id}',    'DepositController@update');
            $router->delete('{id}', 'DepositController@destroy');
        });

        /**
         * Admin/Order
         */
        $router->group([
            'prefix' => 'orders',
        ], function ($router) {
            $router->get('/',       'OrderController@index');
            $router->post('/',      'OrderController@store');
            $router->get('{id}',    'OrderController@show');
            $router->put('{id}',    'OrderController@update');
            $router->delete('{id}', 'OrderController@destroy');
        });

        /**
         * Investors
         *
         */
        $router->group([
            'prefix' => 'investors',
        ], function ($router) {
            $router->get('/',       'InvestorController@index');
            $router->post('/',      'InvestorController@store');
            $router->get('{id}',    'InvestorController@show');
            $router->put('{id}',    'InvestorController@update');
            $router->delete('{id}', 'InvestorController@destroy');
        });

        /**
         * Admins
         *
         */
        $router->group([], function ($router) {
            $router->get('/',       'AdminController@index');
            $router->post('/',      'AdminController@store');
            $router->get('{id}',    'AdminController@show');
            $router->put('{id}',    'AdminController@update');
            $router->delete('{id}', 'AdminController@destroy');
        });
    });
});
