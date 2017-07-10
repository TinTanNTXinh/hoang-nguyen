<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * =============== REPOSITORY ===============
         * */
        /** MAIN **/
        // Position
        $this->app->bind(
            'App\Repositories\PositionRepositoryInterface',
            'App\Repositories\Eloquent\PositionEloquentRepository'
        );

        // User
        $this->app->bind(
            'App\Repositories\UserRepositoryInterface',
            'App\Repositories\Eloquent\UserEloquentRepository'
        );

        // Customer
        $this->app->bind(
            'App\Repositories\CustomerRepositoryInterface',
            'App\Repositories\Eloquent\CustomerEloquentRepository'
        );

        // Postage
        $this->app->bind(
            'App\Repositories\PostageRepositoryInterface',
            'App\Repositories\Eloquent\PostageEloquentRepository'
        );

        // Transport
        $this->app->bind(
            'App\Repositories\TransportRepositoryInterface',
            'App\Repositories\Eloquent\TransportEloquentRepository'
        );

        // Garage
        $this->app->bind(
            'App\Repositories\GarageRepositoryInterface',
            'App\Repositories\Eloquent\GarageEloquentRepository'
        );

        // Truck
        $this->app->bind(
            'App\Repositories\TruckRepositoryInterface',
            'App\Repositories\Eloquent\TruckEloquentRepository'
        );

        // Driver
        $this->app->bind(
            'App\Repositories\DriverRepositoryInterface',
            'App\Repositories\Eloquent\DriverEloquentRepository'
        );

        // DriverTruck
        $this->app->bind(
            'App\Repositories\DriverTruckRepositoryInterface',
            'App\Repositories\Eloquent\DriverTruckEloquentRepository'
        );

        // Oil
        $this->app->bind(
            'App\Repositories\OilRepositoryInterface',
            'App\Repositories\Eloquent\OilEloquentRepository'
        );

        // Lube
        $this->app->bind(
            'App\Repositories\LubeRepositoryInterface',
            'App\Repositories\Eloquent\LubeEloquentRepository'
        );

        // Cost
        $this->app->bind(
            'App\Repositories\CostRepositoryInterface',
            'App\Repositories\Eloquent\CostEloquentRepository'
        );

        // CostOil
        $this->app->bind(
            'App\Repositories\CostOilRepositoryInterface',
            'App\Repositories\Eloquent\CostOilEloquentRepository'
        );

        // CostLube
        $this->app->bind(
            'App\Repositories\CostLubeRepositoryInterface',
            'App\Repositories\Eloquent\CostLubeEloquentRepository'
        );

        // CostParking
        $this->app->bind(
            'App\Repositories\CostParkingRepositoryInterface',
            'App\Repositories\Eloquent\CostParkingEloquentRepository'
        );

        // CostOther
        $this->app->bind(
            'App\Repositories\CostOtherRepositoryInterface',
            'App\Repositories\Eloquent\CostOtherEloquentRepository'
        );

        // InvoiceCustomer
        $this->app->bind(
            'App\Repositories\InvoiceCustomerRepositoryInterface',
            'App\Repositories\Eloquent\InvoiceCustomerEloquentRepository'
        );

        // InvoiceTruck
        $this->app->bind(
            'App\Repositories\InvoiceTruckRepositoryInterface',
            'App\Repositories\Eloquent\InvoiceTruckEloquentRepository'
        );

        /**  **/

        // GroupRole
        $this->app->bind(
            'App\Repositories\GroupRoleRepositoryInterface',
            'App\Repositories\Eloquent\GroupRoleEloquentRepository'
        );

        // Role
        $this->app->bind(
            'App\Repositories\RoleRepositoryInterface',
            'App\Repositories\Eloquent\RoleEloquentRepository'
        );

        // UserRole
        $this->app->bind(
            'App\Repositories\UserRoleRepositoryInterface',
            'App\Repositories\Eloquent\UserRoleEloquentRepository'
        );

        // UserPosition
        $this->app->bind(
            'App\Repositories\UserPositionRepositoryInterface',
            'App\Repositories\Eloquent\UserPositionEloquentRepository'
        );

        // Voucher
        $this->app->bind(
            'App\Repositories\VoucherRepositoryInterface',
            'App\Repositories\Eloquent\VoucherEloquentRepository'
        );

        // Formula
        $this->app->bind(
            'App\Repositories\FormulaRepositoryInterface',
            'App\Repositories\Eloquent\FormulaEloquentRepository'
        );

        // Product
        $this->app->bind(
            'App\Repositories\ProductRepositoryInterface',
            'App\Repositories\Eloquent\ProductEloquentRepository'
        );

        // ProductType
        $this->app->bind(
            'App\Repositories\ProductTypeRepositoryInterface',
            'App\Repositories\Eloquent\ProductTypeEloquentRepository'
        );

        // TransportVoucher
        $this->app->bind(
            'App\Repositories\TransportVoucherRepositoryInterface',
            'App\Repositories\Eloquent\TransportVoucherEloquentRepository'
        );

        // TransportFormula
        $this->app->bind(
            'App\Repositories\TransportFormulaRepositoryInterface',
            'App\Repositories\Eloquent\TransportFormulaEloquentRepository'
        );

        // FormulaSample
        $this->app->bind(
            'App\Repositories\FormulaSampleRepositoryInterface',
            'App\Repositories\Eloquent\FormulaSampleEloquentRepository'
        );

        // Unit
        $this->app->bind(
            'App\Repositories\UnitRepositoryInterface',
            'App\Repositories\Eloquent\UnitEloquentRepository'
        );

        // CustomerType
        $this->app->bind(
            'App\Repositories\CustomerTypeRepositoryInterface',
            'App\Repositories\Eloquent\CustomerTypeEloquentRepository'
        );

        // GarageType
        $this->app->bind(
            'App\Repositories\GarageTypeRepositoryInterface',
            'App\Repositories\Eloquent\GarageTypeEloquentRepository'
        );

        // TruckType
        $this->app->bind(
            'App\Repositories\TruckTypeRepositoryInterface',
            'App\Repositories\Eloquent\TruckTypeEloquentRepository'
        );

        // Field
        $this->app->bind(
            'App\Repositories\FieldRepositoryInterface',
            'App\Repositories\Eloquent\FieldEloquentRepository'
        );

        // StaffCustomer
        $this->app->bind(
            'App\Repositories\StaffCustomerRepositoryInterface',
            'App\Repositories\Eloquent\StaffCustomerEloquentRepository'
        );

        // TransportInvoice
        $this->app->bind(
            'App\Repositories\TransportInvoiceRepositoryInterface',
            'App\Repositories\Eloquent\TransportInvoiceEloquentRepository'
        );

        // ProductCode
        $this->app->bind(
            'App\Repositories\ProductCodeRepositoryInterface',
            'App\Repositories\Eloquent\ProductCodeEloquentRepository'
        );

        // FuelCustomer
        $this->app->bind(
            'App\Repositories\FuelCustomerRepositoryInterface',
            'App\Repositories\Eloquent\FuelCustomerEloquentRepository'
        );

        // InvoiceDetail
        $this->app->bind(
            'App\Repositories\InvoiceDetailRepositoryInterface',
            'App\Repositories\Eloquent\InvoiceDetailEloquentRepository'
        );

        // File
        $this->app->bind(
            'App\Repositories\FileRepositoryInterface',
            'App\Repositories\Eloquent\FileEloquentRepository'
        );

        /*
         * =============== SERVICE ===============
         * */

        // AuthService
        $this->app->bind(
            'App\Services\AuthServiceInterface',
            'App\Services\Implement\AuthService'
        );

        // InvoiceCustomer
        $this->app->bind(
            'App\Services\InvoiceCustomerServiceInterface',
            'App\Services\Implement\InvoiceCustomerService'
        );

        // InvoiceTruck
        $this->app->bind(
            'App\Services\InvoiceTruckServiceInterface',
            'App\Services\Implement\InvoiceTruckService'
        );

        // CostLube
        $this->app->bind(
            'App\Services\CostLubeServiceInterface',
            'App\Services\Implement\CostLubeService'
        );

        // CostOil
        $this->app->bind(
            'App\Services\CostOilServiceInterface',
            'App\Services\Implement\CostOilService'
        );

        // CostOther
        $this->app->bind(
            'App\Services\CostOtherServiceInterface',
            'App\Services\Implement\CostOtherService'
        );

        // CostParking
        $this->app->bind(
            'App\Services\CostParkingServiceInterface',
            'App\Services\Implement\CostParkingService'
        );

        // Customer
        $this->app->bind(
            'App\Services\CustomerServiceInterface',
            'App\Services\Implement\CustomerService'
        );

        // Position
        $this->app->bind(
            'App\Services\PositionServiceInterface',
            'App\Services\Implement\PositionService'
        );

        // Driver
        $this->app->bind(
            'App\Services\DriverServiceInterface',
            'App\Services\Implement\DriverService'
        );

        // DriverTruck
        $this->app->bind(
            'App\Services\DriverTruckServiceInterface',
            'App\Services\Implement\DriverTruckService'
        );

        // FormulaSample
        $this->app->bind(
            'App\Services\FormulaSampleServiceInterface',
            'App\Services\Implement\FormulaSampleService'
        );

        // Garage
        $this->app->bind(
            'App\Services\GarageServiceInterface',
            'App\Services\Implement\GarageService'
        );

        // Lube
        $this->app->bind(
            'App\Services\LubeServiceInterface',
            'App\Services\Implement\LubeService'
        );

        // Oil
        $this->app->bind(
            'App\Services\OilServiceInterface',
            'App\Services\Implement\OilService'
        );

        // Postage
        $this->app->bind(
            'App\Services\PostageServiceInterface',
            'App\Services\Implement\PostageService'
        );

        // Product
        $this->app->bind(
            'App\Services\ProductServiceInterface',
            'App\Services\Implement\ProductService'
        );

        // StaffCustomer
        $this->app->bind(
            'App\Services\StaffCustomerServiceInterface',
            'App\Services\Implement\StaffCustomerService'
        );

        // Transport
        $this->app->bind(
            'App\Services\TransportServiceInterface',
            'App\Services\Implement\TransportService'
        );

        // Truck
        $this->app->bind(
            'App\Services\TruckServiceInterface',
            'App\Services\Implement\TruckService'
        );

        // TruckType
        $this->app->bind(
            'App\Services\TruckTypeServiceInterface',
            'App\Services\Implement\TruckTypeService'
        );

        // Unit
        $this->app->bind(
            'App\Services\UnitServiceInterface',
            'App\Services\Implement\UnitService'
        );

        // User
        $this->app->bind(
            'App\Services\UserServiceInterface',
            'App\Services\Implement\UserService'
        );

        // Voucher
        $this->app->bind(
            'App\Services\VoucherServiceInterface',
            'App\Services\Implement\VoucherService'
        );

        // File
        $this->app->bind(
            'App\Services\FileServiceInterface',
            'App\Services\Implement\FileService'
        );
    }
}
