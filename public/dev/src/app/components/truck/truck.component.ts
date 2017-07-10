import {Component, OnInit, ViewChild} from '@angular/core';

import {HttpClientService} from '../../services/httpClient.service';
import {DateHelperService} from '../../services/helpers/date.helper';
import {ToastrHelperService} from '../../services/helpers/toastr.helper';
import {DomHelperService} from '../../services/helpers/dom.helper';
import {FileHelperService} from '../../services/helpers/file.helper';

@Component({
    selector: 'app-truck',
    templateUrl: './truck.component.html'
})
export class TruckComponent implements OnInit
    , ICommon, ICrud, IDatePicker, ISearch {

    /* ===== MY VARIABLES ===== */
    public trucks: any[] = [];
    public trucks_search: any[] = [];
    public truck: any;

    public truck_types: any[] = [];
    public garages: any[] = [];
    public statuses: string[] = [
        'Chưa phân tài',
        'Đang giao hàng',
        'Đã giao hàng',
        'Không giao được'
    ];

    /* ===== VARIABLES FOR FILES ===== */
    @ViewChild('file')
    file_view_child: any;

    public files: any[] = [];
    public files_of_truck: any[] = [];
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
        this.title = 'Xe';
        this.prefix_url = 'trucks';
        this.range_date = this.dateHelperService.range_date;
        this.datepickerSettings = this.dateHelperService.datepickerSettings;
        this.timepickerSettings = this.dateHelperService.timepickerSettings;
        this.header = {
            garage_name: {
                title: 'Nhà xe',
                data_type: 'TEXT'
            },
            truck_type_name: {
                title: 'Loại',
                data_type: 'TEXT'
            },
            truck_type_weight: {
                title: 'Trọng tải',
                data_type: 'NUMBER'
            },
            area_code_number_plate: {
                title: 'Xe',
                data_type: 'TEXT'
            },
            status: {
                title: 'Trạng thái',
                data_type: 'TEXT'
            },
            trademark: {
                title: 'Hãng',
                data_type: 'TEXT'
            },
            year_of_manufacture: {
                title: 'Năm sản xuất',
                data_type: 'NUMBER'
            },
            owner: {
                title: 'Chủ xe',
                data_type: 'TEXT'
            },
            length: {
                title: 'Dài',
                data_type: 'TEXT'
            },
            width: {
                title: 'Rộng',
                data_type: 'TEXT'
            },
            height: {
                title: 'Cao',
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
        this.trucks = [];
        this.trucks_search = arr_data['trucks'];
        this.truck_types = arr_data['truck_types'];
        this.garages = arr_data['garages'];

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
        this.truck = this.trucks.find(o => o.id == id);

        this.loadFiles(id);
    }

    clearOne(): void {
        this.truck = {
            area_code: '',
            number_plate: '',
            trademark: '',
            year_of_manufacture: 2000,
            owner: '',
            length: 0,
            width: 0,
            height: 0,
            status: 'Chưa phân tài',
            note: '',
            garage_id: 0,
            truck_type_id: 0
        };

        this.clearFiles();
    }

    addOne(): void {
        if (!this.validateOne()) return;

        this.httpClientService.post(this.prefix_url, {"truck": this.truck}).subscribe(
            (success: any) => {
                // Upload File
                this.uploadFile(success.id);

                this.reloadData(success);
                this.clearOne();
                this.toastrHelperService.showToastr('success', 'Thêm thành công!');
            },
            (error: any) => {
                this.toastrHelperService.showToastrErrors(error.json());
            }
        );
    }

    updateOne(): void {
        if (!this.validateOne()) return;

        this.httpClientService.put(this.prefix_url, {"truck": this.truck}).subscribe(
            (success: any) => {
                this.reloadData(success);

                // Upload File
                this.uploadFile(this.truck.id);
                
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
        if (this.truck.area_code == '') {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Mã vùng ${this.title} không được để trống!`);
        }
        if (this.truck.number_plate == '') {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Số xe không được để trống!`);
        }
        if (this.truck.truck_type_id == 0) {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Loại ${this.title} không được để trống!`);
        }
        if (this.truck.garage_id == 0) {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Nhà ${this.title} không được để trống!`);
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
        this.trucks = arr_data['trucks'];
    }

    clearSearch(): void {
        this.datepicker_from = null;
        this.datepicker_to = null;
        this.filtering = {
            from_date: '',
            to_date: '',
            range: '',
            truck_id: 0,
            garage_id: 0,
            truck_type_id: 0,
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

    /* ===== FILE ===== */
    public getFileList(): FileList {
        return this.file_view_child.nativeElement.files;
    }

    public clearFiles(): void {
        this.files_of_truck = [];
        this.file_view_child.nativeElement.value = '';
    }

    public loadFiles(one_id: number): void {
        this.files_of_truck = this.files.filter(o => o.table_name == 'trucks' && o.table_id == one_id);

        if(this.files_of_truck.length > 0)
            this.download_url = this.fileHelperService.createDownloadUrl(this.files_of_truck[0].id);
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
                table_name: "trucks",
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

                this.loadFiles(this.truck.id);
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