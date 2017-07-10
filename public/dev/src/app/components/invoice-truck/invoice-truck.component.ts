import {Component, OnInit, ViewChild} from '@angular/core';

import {HttpClientService} from '../../services/httpClient.service';
import {DateHelperService} from '../../services/helpers/date.helper';
import {ToastrHelperService} from '../../services/helpers/toastr.helper';
import {DomHelperService} from '../../services/helpers/dom.helper';
import {FileHelperService} from '../../services/helpers/file.helper';

@Component({
    selector: 'app-invoice-truck',
    templateUrl: './invoice-truck.component.html'
})
export class InvoiceTruckComponent implements OnInit
    , ICommon, ICrud, IDatePicker, ISearch {

    /* ===== MY VARIABLES ===== */
    public invoice_trucks: any[] = [];
    public invoice_truck: any;
    public transport_ids: number[] = [];

    public trucks: any[] = [];
    public transports: any[] = [];

    public invoice_date: Date = new Date();
    public payment_date: Date = new Date();

    public transport: any;

    /* ===== MY VARIABLES MASTER-DETAIL ===== */
    public setup_master_detail = {
        link: 'invoice-trucks/search/truck',
        json_name: 'transports'
    };
    public header_master = {
        area_code_number_plate: {
            title: 'Xe',
            data_type: 'TEXT'
        }
    };
    public header_detail = {
        fd_transport_date: {
            title: 'Ngày vận chuyển',
            data_type: 'DATETIME',
            prop_name: 'transport_date'
        },
        customer_fullname: {
            title: 'Khách hàng',
            data_type: 'TEXT'
        },
        truck_area_code_number_plate: {
            title: 'Xe',
            data_type: 'TEXT'
        },
        quantum_product: {
            title: 'Lượng hàng',
            data_type: 'NUMBER'
        },
        fc_delivery_real: {
            title: 'Giao xe',
            data_type: 'NUMBER',
            prop_name: 'delivery_real'
        },
        fc_carrying_real: {
            title: 'Bốc xếp',
            data_type: 'NUMBER',
            prop_name: 'carrying_real'
        },
        fc_parking_real: {
            title: 'Neo đêm',
            data_type: 'NUMBER',
            prop_name: 'parking_real'
        },
        fc_fine_real: {
            title: 'Công an',
            data_type: 'NUMBER',
            prop_name: 'fine_real'
        },
        fc_phi_tang_bo_real: {
            title: 'Phí tăng bo',
            data_type: 'NUMBER',
            prop_name: 'phi_tang_bo_real'
        },
        fc_add_score_real: {
            title: 'Thêm điểm',
            data_type: 'NUMBER',
            prop_name: 'add_score_real'
        }
    };
    public action_detail: any = {
        EDIT_COST_IN_TRANSPORT: {
            visible: true,
            caption: 'Cập nhật chi phí',
            icon: 'fa fa-pencil',
            btn_class: 'btn m-b-xs btn-sm btn-warning btn-addon',
            show_modal: false,
            force_selected_row: false
        },
        PTT: {
            visible: true,
            caption: 'Phiếu thanh toán',
            icon: 'fa fa-plus',
            btn_class: 'btn m-b-xs btn-sm btn-info btn-addon',
            show_modal: false,
            force_selected_row: true
        }
    };

    /* ===== VARIABLES FOR FILES ===== */
    @ViewChild('file')
    file_view_child: any;

    public files: any[] = [];
    public files_of_invoice: any[] = [];
    public header_file: any;
    public action_data_file: any;
    public download_url: string = '';

    /* ===== ICOMMON ===== */
    title: string;
    placeholder_code: string;
    prefix_url: string;
    isLoading: boolean;
    header: any;
    action_data: any;

    /* ===== ICRUD ===== */
    modal: any;
    isEdit: boolean;

    /* ===== IDATEPICKER ===== */
    range_date: any[];
    datepickerSettings: any;
    timepickerSettings: any;
    datepicker_from: Date;
    datepicker_to: Date;
    datepickerToOpts: any = {};

    /* ===== ISEARCH ===== */
    filtering: any;

    constructor(private httpClientService: HttpClientService
        , private dateHelperService: DateHelperService
        , private toastrHelperService: ToastrHelperService
        , private domHelperService: DomHelperService
        , private fileHelperService: FileHelperService) {
    }

    ngOnInit(): void {
        this.title = 'Công nợ nhà xe';
        this.prefix_url = 'invoice-trucks';
        this.range_date = this.dateHelperService.range_date;
        this.datepickerSettings = this.dateHelperService.datepickerSettings;
        this.timepickerSettings = this.dateHelperService.timepickerSettings;
        this.header = {
            type1: {
                title: 'type1',
                data_type: 'TEXT'
            },
            type3: {
                title: 'Loại',
                data_type: 'TEXT'
            },
            truck_area_number_plate: {
                title: 'Xe',
                data_type: 'TEXT'
            },
            fc_total_delivery: {
                title: 'Tổng giao xe',
                data_type: 'NUMBER',
                prop_name: 'total_delivery'
            },
            fc_total_cost: {
                title: 'Tổng chi phí',
                data_type: 'NUMBER',
                prop_name: 'total_cost'
            },
            fc_total_cost_in_transport: {
                title: 'Tổng chi phí',
                data_type: 'NUMBER',
                prop_name: 'total_cost_in_transport'
            },
            fc_total_pay: {
                title: 'Tiền xuất',
                data_type: 'NUMBER',
                prop_name: 'total_pay'
            },
            fc_total_paid: {
                title: 'Tổng đã trả',
                data_type: 'NUMBER',
                prop_name: 'total_paid'
            },
            fd_invoice_date: {
                title: 'Ngày hóa đơn',
                data_type: 'DATETIME',
                prop_name: 'invoice_date'
            },
            fd_payment_date: {
                title: 'Ngày thanh toán',
                data_type: 'DATETIME',
                prop_name: 'payment_date'
            },
            receiver: {
                title: 'Người nhận',
                data_type: 'TEXT'
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

        this.remindPaymentInvoice();

        this.clearTransport();

        this.header_file = {
            name: {
                title: 'Tên',
                data_type: 'TEXT'
            },
            size: {
                title: 'Kích thước',
                data_type: 'NUMBER'
            }
        };

        this.action_data_file = {
            DELETE: {
                visible: true,
                caption: 'Xóa',
                icon: 'fa fa-trash-o',
                btn_class: 'btn m-b-xs btn-sm btn-danger btn-addon',
                show_modal: false,
                force_selected_row: true
            },
            DOWNLOAD: {
                visible: true,
                caption: 'Tải',
                icon: 'fa fa-arrow-down',
                btn_class: 'btn m-b-xs btn-sm btn-success btn-addon',
                show_modal: false,
                force_selected_row: true
            }
        };
    }

    /* ===== ICOMMON ===== */
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
        this.invoice_trucks = [];

        this.trucks = arr_data['trucks'];
        
        this.reloadDataFiles(arr_data);
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

    /* ===== ICRUD ===== */
    loadOne(id: number): void {
        this.invoice_truck = this.invoice_trucks.find(o => o.id == id);

        this.invoice_truck.paid_amt = 0;

        this.setDataOneToGlobal();

        this.loadFiles(id);
    }

    clearOne(): void {
        this.invoice_truck = {
            type1: 'NORMAL',
            type3: 'TRUCK-PTT',
            truck_id: 0,
            total_delivery: 0,
            total_cost: 0,
            total_cost_in_transport: 0,
            total_pay: 0,
            total_paid: 0,
            paid_amt: 0,
            invoice_date: '',
            payment_date: '',
            receiver: '',
            note: ''
        };

        this.transports = [];

        this.clearFiles();
    }

    addOne(): void {
        if (!this.validateOne()) return;

        this.setDataGlobalToOne();

        let data = {
            invoice_truck: this.invoice_truck,
            transport_ids: this.transport_ids
        };

        this.httpClientService.post(this.prefix_url, {"invoice_truck": data}).subscribe(
            (success: any) => {
                // Upload File
                this.uploadFile(success.id);
                
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

        let data = {
            invoice_truck: this.invoice_truck
        };

        this.httpClientService.put(this.prefix_url, {"invoice_truck": data}).subscribe(
            (success: any) => {
                // Upload File
                this.uploadFile(this.invoice_truck.id);
                
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

    /* ===== IDATEPICKER ===== */
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

    /* ===== ISEARCH ===== */
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
        this.invoice_trucks = arr_data['invoice_trucks'];
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

    /* ===== FUNCTION ACTION ===== */
    public addInvoice(event: any): void {
        console.log(event.mode);
        switch (event.mode) {
            case 'EDIT_COST_IN_TRANSPORT':
                let transport_id = this.validateEditCostInTransport(event.data);
                if (transport_id == 0) return;

                this.transport = this.transports.find(o => o.id == transport_id);
                this.domHelperService.toggleModal('modal-edit-cost-in-transport');
                break;
            case 'PTT':
                this.setTransportIds(event.data);
                this.readComputeData();
                break;
            default:
                break;
        }
    }

    public computeAfterVat(): void {
        this.invoice_truck.total_pay = this.invoice_truck.total_delivery + this.invoice_truck.total_cost_in_transport - this.invoice_truck.total_cost;
    }

    public updateCostInTransport(): void {
        this.httpClientService.put(`${this.prefix_url}/update-cost-in-transport`, {"transport": this.transport}).subscribe(
            (success: any) => {
                this.reloadDataTransports(success);
                this.clearTransport();
                this.toastrHelperService.showToastr('success', 'Cập nhật thành công!');
                this.domHelperService.toggleModal('modal-edit-cost-in-transport');
            },
            (error: any) => {
                for (let err of error.json()['msg']) {
                    this.toastrHelperService.showToastr('error', err);
                }
            }
        );
    }

    /* ===== FUNCTION ===== */
    private setDataGlobalToOne(): void {
        this.invoice_truck.invoice_date = this.dateHelperService.getDate(this.invoice_date);
        this.invoice_truck.payment_date = this.dateHelperService.getDate(this.payment_date);
    }

    private setDataOneToGlobal(): void {
        this.invoice_date = new Date(this.invoice_truck.invoice_date);
        this.payment_date = new Date(this.invoice_truck.payment_date);
    }

    // PTT
    private setTransportIds(data: any[]): void {
        this.transport_ids = data.map(o => o.id);
    }

    private readComputeData(): void {
        this.httpClientService.get(`invoice-trucks/compute/truck?query=${JSON.stringify({transport_ids: this.transport_ids})}`).subscribe(
            (success: any) => {
                this.setComputeData(success);
                this.computeAfterVat();
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    private setComputeData(data: any): void {
        this.invoice_truck.total_delivery = data.total_delivery;
        this.invoice_truck.total_cost = data.total_cost;
        this.invoice_truck.total_cost_in_transport = data.total_cost_in_transport;
        this.invoice_truck.truck_id = data.truck_id;
    }

    // EDIT COST IN TRANSPORT
    private clearTransport(): void {
        this.transport = {
            carrying_real: 0,
            parking_real: 0,
            fine_real: 0,
            phi_tang_bo_real: 0,
            add_score_real: 0
        };
    }

    private validateEditCostInTransport(data: any[]): number {
        let transport_ids: number[] = data.map(o => o.id);
        if (transport_ids.length == 0 || transport_ids.length > 1) {
            this.toastrHelperService.showToastr('warning', 'Vui lòng chọn một dòng dữ liệu!');
            return 0;
        }
        return transport_ids[0];
    }

    private reloadDataTransports(data: any[]) {
        this.transports = data['transports'];
    }

    private remindPaymentInvoice(): void {
        this.httpClientService.get(`${this.prefix_url}/remind-payment-invoice`).subscribe(
            (success: any) => {
                let invoices = success.invoice_trucks;
                if (invoices.length > 0) {
                    this.toastrHelperService.showToastr('info', `Có ${invoices.length} phiếu thanh toán cần thanh toán.`, 'Nhắc nhở')
                }
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    /* ===== FILE ===== */
    public getFileList(): FileList {
        return this.file_view_child.nativeElement.files;
    }

    public clearFiles(): void {
        this.files_of_invoice = [];
        this.file_view_child.nativeElement.value = '';
    }

    public loadFiles(one_id: number): void {
        this.files_of_invoice = this.files.filter(o => o.table_name == 'invoices' && o.table_id == one_id);

        if(this.files_of_invoice.length > 0)
            this.download_url = this.fileHelperService.createDownloadUrl(this.files_of_invoice[0].id);
    }

    public reloadDataFiles(arr_data: any[]): void {
        this.files = arr_data['files'];
    }

    public actionCrudFile(obj: any): void {
        switch (obj.mode) {
            case 'DELETE':
                this.deleteFile(obj.data.id);
                break;
            case 'DOWNLOAD':
                this.domHelperService.getElementById('download-file').click();
                // this.downloadFile(obj.data.id);
                break;
            default:
                break;
        }
    }

    public uploadFile(table_id: number): void {
        if (this.getFileList().length > 0) {
            let formData: FormData = this.fileHelperService.createFormData({
                table_name: "invoices",
                table_id: table_id
            }, this.getFileList());

            this.httpClientService.post('files/upload', formData).subscribe(
                (success: any) => {
                    this.reloadDataFiles(success);
                },
                (error: any) => {
                    for (let err of error.json()['msg']) {
                        this.toastrHelperService.showToastr('error', err);
                    }
                }
            );
        }
    }

    public deleteFile(id: number): void {
        this.httpClientService.delete(`files/${id}`).subscribe(
            (success: any) => {
                this.reloadDataFiles(success);
                this.toastrHelperService.showToastr('success', 'Xóa thành công!');

                this.loadFiles(this.invoice_truck.id);
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    public downloadFile(id: number): void {
        this.httpClientService.get(`files/download/${id}`, 'text').subscribe(
            (success: any) => {
                this.fileHelperService.downloadFile(success, 'alo.pdf','application/pdf')
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    public selectedRowFile(obj: any): void {
        this.download_url = this.fileHelperService.createDownloadUrl(obj.data.id);
    }

    public refreshFiles(): void {
        this.domHelperService.getElementById('refresh-files').click();
    }
}