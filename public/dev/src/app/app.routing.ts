import {Routes, RouterModule} from '@angular/router';

// My share components
import {DashboardComponent} from './layout-components/dashboard/dashboard.component';
import {LoginComponent} from './layout-components/login/login.component';
import {ChangePasswordComponent} from './layout-components/change-password/change-password.component';

// My components
import {PositionComponent} from './components/position/position.component';
import {UserComponent} from './components/user/user.component';
import {CustomerComponent} from './components/customer/customer.component';
import {StaffCustomerComponent} from './components/staff-customer/staff-customer.component';
import {PostageComponent} from './components/postage/postage.component';
import {TransportComponent} from './components/transport/transport.component';
import {GarageComponent} from './components/garage/garage.component';
import {TruckComponent} from './components/truck/truck.component';
import {DriverComponent} from './components/driver/driver.component';
import {DriverTruckComponent} from './components/driver-truck/driver-truck.component';
import {LubeComponent} from './components/lube/lube.component';
import {OilComponent} from './components/oil/oil.component';
import {CostOilComponent} from './components/cost-oil/cost-oil.component';
import {CostLubeComponent} from './components/cost-lube/cost-lube.component';
import {CostParkingComponent} from './components/cost-parking/cost-parking.component';
import {CostOtherComponent} from './components/cost-other/cost-other.component';
import {InvoiceCustomerComponent} from './components/invoice-customer/invoice-customer.component';
import {InvoiceTruckComponent} from './components/invoice-truck/invoice-truck.component';
import {CanActivateViaPosition} from "./middlewares/CanActivateViaPosition";

import {UnitComponent} from './components/unit/unit.component';
import {ProductComponent} from './components/product/product.component';
import {VoucherComponent} from './components/voucher/voucher.component';
import {FormulaSampleComponent} from './components/formula-sample/formula-sample.component';
import {TruckTypeComponent} from './components/truck-type/truck-type.component';

// My middleware

const APP_ROUTES: Routes = [
    {path: '', redirectTo: 'dashboards', pathMatch: 'full'},
    {path: 'dashboards', component: DashboardComponent},
    {path: 'login', component: LoginComponent},
    {path: 'change-password', component: ChangePasswordComponent},

    {path: 'positions', component: PositionComponent, canActivate: [CanActivateViaPosition]},
    {path: 'users', component: UserComponent},

    {path: 'customers', component: CustomerComponent},
    {path: 'staff-customers', component: StaffCustomerComponent},
    {path: 'postages', component: PostageComponent},
    {path: 'transports', component: TransportComponent},

    {path: 'garages', component: GarageComponent},
    {path: 'trucks', component: TruckComponent},
    {path: 'drivers', component: DriverComponent},
    {path: 'driver-trucks', component: DriverTruckComponent},

    {path: 'oils', component: OilComponent},
    {path: 'lubes', component: LubeComponent},

    {path: 'cost-oils', component: CostOilComponent},
    {path: 'cost-lubes', component: CostLubeComponent},
    {path: 'cost-parkings', component: CostParkingComponent},
    {path: 'cost-others', component: CostOtherComponent},
    //
    {path: 'invoice-customers', component: InvoiceCustomerComponent},
    {path: 'invoice-trucks', component: InvoiceTruckComponent},
    //
    // {path: 'report-revenues', component: null},
    // {path: 'report-transports', component: null},

    {path: 'units', component: UnitComponent},
    {path: 'products', component: ProductComponent},
    {path: 'vouchers', component: VoucherComponent},
    {path: 'formula-samples', component: FormulaSampleComponent},
    {path: 'truck-types', component: TruckTypeComponent},
];

export const routing = RouterModule.forRoot(APP_ROUTES);
