<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    Route::group(['middleware' => []], function () {
        Route::post('/authentication', 'AuthController@postAuthentication');
    });

    // Co header la "Bearer + token" thi duoc vao
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::get('/authorization', 'AuthController@getAuthorization');

        /** MAIN **/
        // Position
        Route::group(['middleware' => 'position', 'prefix' => 'positions'], function () {
            Route::get('/', 'PositionController@getReadAll');
            Route::get('/search', 'PositionController@getSearchOne');
            Route::get('/{id}', 'PositionController@getReadOne');
            Route::post('/', 'PositionController@postCreateOne');
            Route::put('/', 'PositionController@putUpdateOne');
            Route::patch('/', 'PositionController@patchDeactivateOne');
            Route::delete('/{id}', 'PositionController@deleteDeleteOne');
        });

        // User
        Route::group(['middleware' => 'user', 'prefix' => 'users'], function () {
            Route::get('/', 'UserController@getReadAll');
            Route::get('/search', 'UserController@getSearchOne');
            Route::get('/{id}', 'UserController@getReadOne');
            Route::post('/', 'UserController@postCreateOne');
            Route::post('/change-password', 'UserController@postChangePassword');
            Route::put('/', 'UserController@putUpdateOne');
            Route::patch('/', 'UserController@patchDeactivateOne');
            Route::delete('/{id}', 'UserController@deleteDeleteOne');
        });

        // Customer
        Route::group(['middleware' => 'customer', 'prefix' => 'customers'], function () {
            Route::get('/', 'CustomerController@getReadAll');
            Route::get('/search', 'CustomerController@getSearchOne');
            Route::get('/{id}', 'CustomerController@getReadOne');
            Route::post('/', 'CustomerController@postCreateOne');
            Route::put('/', 'CustomerController@putUpdateOne');
            Route::patch('/', 'CustomerController@patchDeactivateOne');
            Route::delete('/{id}', 'CustomerController@deleteDeleteOne');
        });

        // Postage
        Route::group(['middleware' => 'postage', 'prefix' => 'postages'], function () {
            Route::get('/search/customer/{customer_id}', 'PostageController@getReadByCustomerId');

            Route::get('/', 'PostageController@getReadAll');
            Route::get('/search', 'PostageController@getSearchOne');
            Route::get('/{id}', 'PostageController@getReadOne');
            Route::post('/', 'PostageController@postCreateOne');
            Route::put('/', 'PostageController@putUpdateOne');
            Route::patch('/', 'PostageController@patchDeactivateOne');
            Route::delete('/{id}', 'PostageController@deleteDeleteOne');
        });

        // Transport
        Route::group(['middleware' => 'transport', 'prefix' => 'transports'], function () {
            Route::get('/find-formulas', 'TransportController@getReadFormulas');
            Route::get('/find-postage', 'TransportController@getReadPostage');

            Route::get('/', 'TransportController@getReadAll');
            Route::get('/search', 'TransportController@getSearchOne');
            Route::get('/{id}', 'TransportController@getReadOne');
            Route::post('/', 'TransportController@postCreateOne');
            Route::put('/', 'TransportController@putUpdateOne');
            Route::patch('/', 'TransportController@patchDeactivateOne');
            Route::delete('/{id}', 'TransportController@deleteDeleteOne');
        });

        // Garage
        Route::group(['middleware' => 'garage', 'prefix' => 'garages'], function () {
            Route::get('/', 'GarageController@getReadAll');
            Route::get('/search', 'GarageController@getSearchOne');
            Route::get('/{id}', 'GarageController@getReadOne');
            Route::post('/', 'GarageController@postCreateOne');
            Route::put('/', 'GarageController@putUpdateOne');
            Route::patch('/', 'GarageController@patchDeactivateOne');
            Route::delete('/{id}', 'GarageController@deleteDeleteOne');
        });

        // Truck
        Route::group(['middleware' => 'truck', 'prefix' => 'trucks'], function () {
            Route::get('/', 'TruckController@getReadAll');
            Route::get('/search', 'TruckController@getSearchOne');
            Route::get('/{id}', 'TruckController@getReadOne');
            Route::post('/', 'TruckController@postCreateOne');
            Route::put('/', 'TruckController@putUpdateOne');
            Route::patch('/', 'TruckController@patchDeactivateOne');
            Route::delete('/{id}', 'TruckController@deleteDeleteOne');
        });

        // Driver
        Route::group(['middleware' => 'driver', 'prefix' => 'drivers'], function () {
            Route::get('/', 'DriverController@getReadAll');
            Route::get('/search', 'DriverController@getSearchOne');
            Route::get('/{id}', 'DriverController@getReadOne');
            Route::post('/', 'DriverController@postCreateOne');
            Route::put('/', 'DriverController@putUpdateOne');
            Route::patch('/', 'DriverController@patchDeactivateOne');
            Route::delete('/{id}', 'DriverController@deleteDeleteOne');
        });

        // DriverTruck
        Route::group(['middleware' => 'driver-truck', 'prefix' => 'driver-trucks'], function () {
            Route::get('/', 'DriverTruckController@getReadAll');
            Route::get('/search', 'DriverTruckController@getSearchOne');
            Route::get('/{id}', 'DriverTruckController@getReadOne');
            Route::post('/', 'DriverTruckController@postCreateOne');
            Route::put('/', 'DriverTruckController@putUpdateOne');
            Route::patch('/', 'DriverTruckController@patchDeactivateOne');
            Route::delete('/{id}', 'DriverTruckController@deleteDeleteOne');
        });

        // Oil
        Route::group(['middleware' => 'oil', 'prefix' => 'oils'], function () {
            Route::get('/find', 'OilController@getReadByApplyDate');

            Route::get('/', 'OilController@getReadAll');
            Route::get('/search', 'OilController@getSearchOne');
            Route::get('/{id}', 'OilController@getReadOne');
            Route::post('/', 'OilController@postCreateOne');
            Route::put('/', 'OilController@putUpdateOne');
            Route::patch('/', 'OilController@patchDeactivateOne');
            Route::delete('/{id}', 'OilController@deleteDeleteOne');
        });

        // Lube
        Route::group(['middleware' => 'lube', 'prefix' => 'lubes'], function () {
            Route::get('/find', 'LubeController@getReadByApplyDate');

            Route::get('/', 'LubeController@getReadAll');
            Route::get('/search', 'LubeController@getSearchOne');
            Route::get('/{id}', 'LubeController@getReadOne');
            Route::post('/', 'LubeController@postCreateOne');
            Route::put('/', 'LubeController@putUpdateOne');
            Route::patch('/', 'LubeController@patchDeactivateOne');
            Route::delete('/{id}', 'LubeController@deleteDeleteOne');
        });

        // CostOil
        Route::group(['middleware' => 'cost-oil', 'prefix' => 'cost-oils'], function () {
            Route::get('/', 'CostOilController@getReadAll');
            Route::get('/search', 'CostOilController@getSearchOne');
            Route::get('/{id}', 'CostOilController@getReadOne');
            Route::post('/', 'CostOilController@postCreateOne');
            Route::put('/', 'CostOilController@putUpdateOne');
            Route::patch('/', 'CostOilController@patchDeactivateOne');
            Route::delete('/{id}', 'CostOilController@deleteDeleteOne');
        });

        // CostLube
        Route::group(['middleware' => 'cost-lube', 'prefix' => 'cost-lubes'], function () {
            Route::get('/', 'CostLubeController@getReadAll');
            Route::get('/search', 'CostLubeController@getSearchOne');
            Route::get('/{id}', 'CostLubeController@getReadOne');
            Route::post('/', 'CostLubeController@postCreateOne');
            Route::put('/', 'CostLubeController@putUpdateOne');
            Route::patch('/', 'CostLubeController@patchDeactivateOne');
            Route::delete('/{id}', 'CostLubeController@deleteDeleteOne');
        });

        // CostParking
        Route::group(['middleware' => 'cost-parking', 'prefix' => 'cost-parkings'], function () {
            Route::get('/', 'CostParkingController@getReadAll');
            Route::get('/search', 'CostParkingController@getSearchOne');
            Route::get('/{id}', 'CostParkingController@getReadOne');
            Route::post('/', 'CostParkingController@postCreateOne');
            Route::put('/', 'CostParkingController@putUpdateOne');
            Route::patch('/', 'CostParkingController@patchDeactivateOne');
            Route::delete('/{id}', 'CostParkingController@deleteDeleteOne');
        });

        // CostOther
        Route::group(['middleware' => 'cost-other', 'prefix' => 'cost-others'], function () {
            Route::get('/', 'CostOtherController@getReadAll');
            Route::get('/search', 'CostOtherController@getSearchOne');
            Route::get('/{id}', 'CostOtherController@getReadOne');
            Route::post('/', 'CostOtherController@postCreateOne');
            Route::put('/', 'CostOtherController@putUpdateOne');
            Route::patch('/', 'CostOtherController@patchDeactivateOne');
            Route::delete('/{id}', 'CostOtherController@deleteDeleteOne');
        });

        // InvoiceCustomer
        Route::group(['middleware' => 'invoice-customer', 'prefix' => 'invoice-customers'], function () {
            Route::get('/search/customer/{customer_id}', 'InvoiceCustomerController@getReadByCustomerIdAndType2');
            Route::get('/remind-payment-invoice', 'InvoiceCustomerController@getReadByPaymentDate');
            Route::get('/compute/customer', 'InvoiceCustomerController@getComputeByTransportIds');
            Route::get('/add-invoice-continue/{invoice_id}', 'InvoiceCustomerController@getComputeByInvoiceId');

            Route::get('/', 'InvoiceCustomerController@getReadAll');
            Route::get('/search', 'InvoiceCustomerController@getSearchOne');
            Route::get('/{id}', 'InvoiceCustomerController@getReadOne');
            Route::post('/', 'InvoiceCustomerController@postCreateOne');
            Route::put('/', 'InvoiceCustomerController@putUpdateOne');
            Route::patch('/', 'InvoiceCustomerController@patchDeactivateOne');
            Route::delete('/{id}', 'InvoiceCustomerController@deleteDeleteOne');
        });

        // InvoiceTruck
        Route::group(['middleware' => 'invoice-truck', 'prefix' => 'invoice-trucks'], function () {
            Route::get('/search/truck/{truck_id}', 'InvoiceTruckController@getReadByTruckIdAndType3');
            Route::get('/remind-payment-invoice', 'InvoiceTruckController@getReadByPaymentDate');
            Route::get('/compute/truck', 'InvoiceTruckController@getComputeByTransportIds');
            Route::put('/update-cost-in-transport', 'InvoiceTruckController@putUpdateCostInTransport');

            Route::get('/', 'InvoiceTruckController@getReadAll');
            Route::get('/search', 'InvoiceTruckController@getSearchOne');
            Route::get('/{id}', 'InvoiceTruckController@getReadOne');
            Route::post('/', 'InvoiceTruckController@postCreateOne');
            Route::put('/', 'InvoiceTruckController@putUpdateOne');
            Route::patch('/', 'InvoiceTruckController@patchDeactivateOne');
            Route::delete('/{id}', 'InvoiceTruckController@deleteDeleteOne');
        });

        // StaffCustomer
        Route::group(['middleware' => 'staff-customer', 'prefix' => 'staff-customers'], function () {
            Route::get('/', 'StaffCustomerController@getReadAll');
            Route::get('/search', 'StaffCustomerController@getSearchOne');
            Route::get('/{id}', 'StaffCustomerController@getReadOne');
            Route::post('/', 'StaffCustomerController@postCreateOne');
            Route::put('/', 'StaffCustomerController@putUpdateOne');
            Route::patch('/', 'StaffCustomerController@patchDeactivateOne');
            Route::delete('/{id}', 'StaffCustomerController@deleteDeleteOne');
        });

        // Unit
        Route::group(['middleware' => 'unit', 'prefix' => 'units'], function () {
            Route::get('/', 'UnitController@getReadAll');
            Route::get('/search', 'UnitController@getSearchOne');
            Route::get('/{id}', 'UnitController@getReadOne');
            Route::post('/', 'UnitController@postCreateOne');
            Route::put('/', 'UnitController@putUpdateOne');
            Route::patch('/', 'UnitController@patchDeactivateOne');
            Route::delete('/{id}', 'UnitController@deleteDeleteOne');
        });

        // Product
        Route::group(['middleware' => 'product', 'prefix' => 'products'], function () {
            Route::get('/', 'ProductController@getReadAll');
            Route::get('/search', 'ProductController@getSearchOne');
            Route::get('/{id}', 'ProductController@getReadOne');
            Route::post('/', 'ProductController@postCreateOne');
            Route::put('/', 'ProductController@putUpdateOne');
            Route::patch('/', 'ProductController@patchDeactivateOne');
            Route::delete('/{id}', 'ProductController@deleteDeleteOne');
        });

        // Voucher
        Route::group(['middleware' => 'voucher', 'prefix' => 'vouchers'], function () {
            Route::get('/', 'VoucherController@getReadAll');
            Route::get('/search', 'VoucherController@getSearchOne');
            Route::get('/{id}', 'VoucherController@getReadOne');
            Route::post('/', 'VoucherController@postCreateOne');
            Route::put('/', 'VoucherController@putUpdateOne');
            Route::patch('/', 'VoucherController@patchDeactivateOne');
            Route::delete('/{id}', 'VoucherController@deleteDeleteOne');
        });

        // FormulaSample
        Route::group(['middleware' => 'formula-sample', 'prefix' => 'formula-samples'], function () {
            Route::get('/', 'FormulaSampleController@getReadAll');
            Route::get('/search', 'FormulaSampleController@getSearchOne');
            Route::get('/{id}', 'FormulaSampleController@getReadOne');
            Route::post('/', 'FormulaSampleController@postCreateOne');
            Route::put('/', 'FormulaSampleController@putUpdateOne');
            Route::patch('/', 'FormulaSampleController@patchDeactivateOne');
            Route::delete('/{id}', 'FormulaSampleController@deleteDeleteOne');
        });

        // TruckType
        Route::group(['middleware' => 'truck-type', 'prefix' => 'truck-types'], function () {
            Route::get('/find/{truck_id}', 'TruckTypeController@getReadByTruckId');

            Route::get('/', 'TruckTypeController@getReadAll');
            Route::get('/search', 'TruckTypeController@getSearchOne');
            Route::get('/{id}', 'TruckTypeController@getReadOne');
            Route::post('/', 'TruckTypeController@postCreateOne');
            Route::put('/', 'TruckTypeController@putUpdateOne');
            Route::patch('/', 'TruckTypeController@patchDeactivateOne');
            Route::delete('/{id}', 'TruckTypeController@deleteDeleteOne');
        });

        //
        Route::group(['middleware' => []], function () {
            // File
            Route::group(['prefix' => 'files'], function () {
                Route::post('/upload', 'FileController@postCreateMultiple');
                Route::get('/download/{id}', 'FileController@getDownloadOne');

                Route::get('/', 'FileController@getReadAll');
                Route::get('/search', 'FileController@getSearchOne');
                Route::get('/{id}', 'FileController@getReadOne');
                Route::post('/', 'FileController@postCreateOne');
                Route::put('/', 'FileController@putUpdateOne');
                Route::patch('/', 'FileController@patchDeactivateOne');
                Route::delete('/{id}', 'FileController@deleteDeleteOne');
            });
        });

    });
});


