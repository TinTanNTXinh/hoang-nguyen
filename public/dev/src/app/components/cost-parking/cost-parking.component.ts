import {Component, OnInit} from '@angular/core';

import {HttpClientService} from '../../services/httpClient.service';
import {DateHelperService} from '../../services/helpers/date.helper';
import {ToastrHelperService} from '../../services/helpers/toastr.helper';
import {DomHelperService} from '../../services/helpers/dom.helper';

@Component({
    selector: 'app-cost-parking',
    templateUrl: './cost-parking.component.html',
    styles: []
})
export class CostParkingComponent implements OnInit
    , ICommon, ICrud, IDatePicker, ISearch {

    /** ===== MY VARIABLES ===== **/
    public cost_parkings: any[] = [];
    public cost_parking: any;
    public trucks: any[] = [];
    public truck_type: any;

    public checkin_date: Date = new Date();
    public checkin_time: Date = new Date();
    public checkout_date: Date = new Date();
    public checkout_time: Date = new Date();

    /** ===== ICOMMON ===== **/
    title: string;
    placeholder_code: string;
    prefix_url: string;
    isLoading: boolean;
    header: any;
    action_data: any;

    /** ===== ICRUD ===== **/
    modal: any;
    isEdit: boolean;

    /** ===== IDATEPICKER ===== **/
    range_date: any[];
    datepickerSettings: any;
    timepickerSettings: any;
    datepicker_from: Date;
    datepicker_to: Date;
    datepickerToOpts: any = {};

    /** ===== ISEARCH ===== **/
    filtering: any;

    constructor(private httpClientService: HttpClientService
        , private dateHelperService: DateHelperService
        , private toastrHelperService: ToastrHelperService
        , private domHelperService: DomHelperService) {
    }

    ngOnInit(): void {
        this.title = 'Chi phí đậu bãi';
        this.prefix_url = 'cost-parkings';
        this.range_date = this.dateHelperService.range_date;
        this.datepickerSettings = this.dateHelperService.datepickerSettings;
        this.timepickerSettings = this.dateHelperService.timepickerSettings;
        this.header = {
            truck_area_code_number_plate: {
                title: 'Xe',
                data_type: 'TEXT'
            },
            truck_type_name: {
                title: 'Loại xe',
                data_type: 'TEXT'
            },
            truck_type_weight: {
                title: 'Trọng tải',
                data_type: 'NUMBER'
            },
            fc_truck_type_unit_price_park: {
                title: 'Đơn giá',
                data_type: 'NUMBER',
                prop_name: 'truck_type_unit_price_park'
            },
            fd_checkin_date: {
                title: 'Ngày đậu',
                data_type: 'DATETIME',
                prop_name: 'checkin_date'
            },
            fd_checkout_date: {
                title: 'Ngày lấy',
                data_type: 'DATETIME',
                prop_name: 'checkout_date'
            },
            total_day: {
                title: 'Số ngày đậu',
                data_type: 'NUMBER'
            },
            fc_after_vat: {
                title: 'Tổng chi phí',
                data_type: 'NUMBER',
                prop_name: 'after_vat'
            },
            note: {
                title: 'Ghi chú',
                data_type: 'TEXT'
            }
        };

        this.modal = {
            id: 0,
            header: '',
            body: '',
            footer: ''
        };

        this.refreshData();
    }

    /** ===== ICOMMON ===== **/
    loadData(): void {
        this.httpClientService.get(this.prefix_url).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.changeLoading(true);
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    reloadData(arr_data: any[]): void {
        this.cost_parkings = [];
        this.trucks = arr_data['trucks'];
    }

    refreshData(): void {
        this.changeLoading(false);
        this.clearOne();
        this.clearSearch();
        this.loadData();
    }

    changeLoading(status: boolean): void {
        this.isLoading = status;
    }

    /** ===== ICRUD ===== **/
    loadOne(id: number): void {
        this.cost_parking = this.cost_parkings.find(o => o.id == id);

        this.setDataOneToGlobal();
    }

    clearOne(): void {
        this.cost_parking = {
            checkin_date: '',
            checkout_date: '',
            total_day: 0,
            after_vat: 0,
            note: '',
            truck_id: 0,

            total_hour: 0,
            truck_type_id: 0,
            truck_type_name: '',
            truck_type_weight: 0,
            truck_type_unit_price_park: 0
        };
        this.truck_type = null;
    }

    addOne(): void {
        if (!this.validateOne()) return;

        this.setDataGlobalToOne();

        this.httpClientService.post(this.prefix_url, {"cost_parking": this.cost_parking}).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.clearOne();
                this.toastrHelperService.showToastr('success', 'Thêm thành công!');
            },
            (error: any) => {
                for (let err of error.json()['msg']) {
                    this.toastrHelperService.showToastr('error', err);
                }
            }
        );
    }

    updateOne(): void {
        if (!this.validateOne()) return;

        this.setDataGlobalToOne();

        this.httpClientService.put(this.prefix_url, {"cost_parking": this.cost_parking}).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.clearOne();
                this.displayEditBtn(false);
                this.toastrHelperService.showToastr('success', 'Cập nhật thành công!');
            },
            (error: any) => {
                this.toastrHelperService.showToastrErrors(error.json());
            }
        );
    }

    deactivateOne(id: number): void {
        this.httpClientService.patch(this.prefix_url, {"id": id}).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.search();
                this.toastrHelperService.showToastr('success', 'Hủy thành công.');
                this.domHelperService.toggleModal('modal-confirm');
            },
            (error: any) => {
                this.toastrHelperService.showToastrErrors(error.json());
            }
        );
    }

    deleteOne(id: number): void {
        this.httpClientService.delete(`${this.prefix_url}/${id}`).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.toastrHelperService.showToastr('success', 'Xóa thành công!');
            },
            (error: any) => {
                this.toastrHelperService.showToastrErrors(error.json());
            }
        );
    }

    confirmDeactivateOne(id: number): void {
        this.deactivateOne(id);
    }

    validateOne(): boolean {
        let flag: boolean = true;
        if (this.cost_parking.truck_id == 0) {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Xe không được để trống!`);
        }
        return flag;
    }

    displayEditBtn(status: boolean): void {
        this.isEdit = status;
    }

    fillDataModal(id: number): void {
        this.modal.id = id;
        this.modal.header = 'Xác nhận';
        this.modal.body = `Có chắc muốn xóa ${this.title} này?`;
        this.modal.footer = 'OK';
    }

    actionCrud(obj: any): void {
        switch (obj.mode) {
            case 'ADD':
                this.clearOne();
                this.displayEditBtn(false);
                this.domHelperService.showTab('menu2');
                break;
            case 'EDIT':
                this.loadOne(obj.data.id);
                this.displayEditBtn(true);
                this.domHelperService.showTab('menu2');
                break;
            case 'DELETE':
                this.fillDataModal(obj.data.id);
                break;
            default:
                break;
        }
    }

    /** ===== IDATEPICKER ===== **/
    handleDateFromChange(dateFrom: Date): void {
        this.datepicker_from = dateFrom;
        this.datepickerToOpts = {
            startDate: dateFrom,
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            icon: this.dateHelperService.icon_calendar,
            placeholder: this.dateHelperService.date_placeholder,
            format: 'dd/mm/yyyy'
        };
    }

    clearDate(event: any, field: string): void {
        if (event == null) {
            switch (field) {
                case 'from':
                    this.filtering.from_date = '';
                    break;
                case 'to':
                    this.filtering.from_date = '';
                    break;
                default:
                    break;
            }
        }
    }

    /** ===== ISEARCH ===== **/
    search(): void {
        if (this.datepicker_from != null && this.datepicker_to != null) {
            let from_date = this.dateHelperService.getDate(this.datepicker_from);
            let to_date = this.dateHelperService.getDate(this.datepicker_to);
            this.filtering.from_date = from_date;
            this.filtering.to_date = to_date;
        }
        this.changeLoading(false);

        this.httpClientService.get(`${this.prefix_url}/search?query=${JSON.stringify(this.filtering)}`).subscribe(
            (success: any) => {
                this.reloadDataSearch(success);
                this.displayColumn();
                this.changeLoading(true);
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    reloadDataSearch(arr_data: any[]): void {
        this.cost_parkings = arr_data['cost_parkings'];
    }

    clearSearch(): void {
        this.datepicker_from = null;
        this.datepicker_to = null;
        this.filtering = {
            from_date: '',
            to_date: '',
            range: '',
            truck_id: 0
        };
    }

    displayColumn(): void {
        let setting = {};
        for (let parent in setting) {
            for (let child of setting[parent]) {
                if (!!this.header[child])
                    this.header[child].visible = !(!!this.filtering[parent]);
            }
        }
    }

    /** ===== FUNCTION ACTION ===== **/
    public findTruckType(event: any): void {
        this.httpClientService.get(`truck-types/find/${event.id}`).subscribe(
            (success: any) => {
                this.truck_type = success.truck_type;
                this.setTruckTypeForOne();

                this.computeAfterVat();
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    public computeAfterVat(): void {
        let checkin_date = this.dateHelperService.joinDateTimeToString(this.checkin_date, this.checkin_time);
        let checkout_date = this.dateHelperService.joinDateTimeToString(this.checkout_date, this.checkout_time);

        let minutes = this.dateHelperService.diff(checkout_date, 'DD/MM/YYYY HH:mm:ss', checkin_date, 'DD/MM/YYYY HH:mm:ss', 'minutes');

        let hours: number = 0;
        let days: number = 0;
        if(minutes > 0) {
            hours = minutes / 60;
            days = this.computeDay(hours);
        }

        this.cost_parking.total_hour = hours;
        this.cost_parking.total_day = days;

        this.cost_parking.after_vat = this.cost_parking.total_day * this.cost_parking.truck_type_unit_price_park;
    }

    /** ===== FUNCTION ===== **/
    private setDataGlobalToOne(): void {
        this.cost_parking.checkin_date = this.dateHelperService.joinDateTimeToString(this.checkin_date, this.checkin_time);
        this.cost_parking.checkout_date = this.dateHelperService.joinDateTimeToString(this.checkout_date, this.checkout_time);
    }

    private setDataOneToGlobal(): void {
        this.checkin_date = new Date(this.cost_parking.checkin_date);
        this.checkin_time = new Date(this.cost_parking.checkin_date);

        this.checkout_date = new Date(this.cost_parking.checkout_date);
        this.checkout_time = new Date(this.cost_parking.checkout_date);
    }

    private setTruckTypeForOne(): void {
        this.cost_parking.truck_type_id = this.truck_type.id;
        this.cost_parking.truck_type_name = this.truck_type.name;
        this.cost_parking.truck_type_weight = this.truck_type.weight;
        this.cost_parking.truck_type_unit_price_park = this.truck_type.unit_price_park;
    }

    private computeDay (hours: number): number
    {
        let x: number = hours % 24;
        let y: number = hours / 24;
        if (x != 0) {
            if (x < 1) {
                x = 0;
            }
            else if (1 <= x && x <= 3) {
                x = 0.5;
            }
            else if (x > 3) {
                x = 1;
            }
        }
        return Math.floor(y) + x;
    }
}