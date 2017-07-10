import {Component, OnInit, Input, Output, EventEmitter} from '@angular/core';

import {HttpClientService} from '../../services/httpClient.service';
import {ToastrHelperService} from '../../services/helpers/toastr.helper';
import {PaginationHelperService} from '../../services/helpers/pagination.helper';
import {DomHelperService} from '../../services/helpers/dom.helper';
import {DateHelperService} from '../../services/helpers/date.helper';

@Component({
    selector: 'master-detail',
    templateUrl: './master-detail.component.html',
    styleUrls: ['./master-detail.component.css']
})
export class MasterDetailComponent implements OnInit {

    /** ===== VARIABLES ===== **/
    public dropdownRow: number = 0;
    public highlightRowsDetail: number[] = [];
    public selectedRowMaster: number = 0;
    public setClickedRowMaster: Function;
    public selectedRowDetail: number = 0;
    public setClickedRowDetail: Function;
    public isAsc: boolean = true;
    public master_data: any[] = [];

    /** ===== VARIABLES PAGINATION ===== **/
    public pager: any = {};
    public pagedItems: any[];
    private pageSize: number = 10;

    constructor(private httpClientService: HttpClientService
        , private toastrHelperService: ToastrHelperService
        , private paginationHelperService: PaginationHelperService
        , private domHelperService: DomHelperService
        , private dateHelperService: DateHelperService) {

        this.clearSelectedAndHighlightRowsDetail();
        this.setClickedRowMaster = function (index) {
            this.selectedRowMaster = index;
        };
        this.setClickedRowDetail = function (index) {
            switch (this.settingClassDetail) {
                case 'active':
                    this.selectedRowDetail = index;
                    break;
                case 'highlight':
                    let index_in_detail_data: number = index;
                    let found = this.highlightRowsDetail.find(o => o == index_in_detail_data);
                    if (typeof found === "undefined")
                        this.highlightRowsDetail.push(index_in_detail_data);
                    else {
                        let index_in_page = this.highlightRowsDetail.indexOf(index_in_detail_data);
                        this.highlightRowsDetail.splice(index_in_page, 1);
                    }
                    break;
                default:
                    break;
            }
        };
    }

    ngOnInit() {
        this.setSortPropMaster();
    }

    /** ===== INPUT ===== **/
    @Input() setup: any = {};
    @Input() header_master: any = {};
    @Input() header_detail: any = {};
    @Input() action_detail: any = {
        ADD: {
            visible: true,
            caption: 'Thêm',
            icon: 'fa fa-plus',
            btn_class: 'btn m-b-xs btn-sm btn-primary btn-addon',
            show_modal: false,
            force_selected_row: false
        },
        EDIT: {
            visible: true,
            caption: 'Cập nhật',
            icon: 'fa fa-pencil',
            btn_class: 'btn m-b-xs btn-sm btn-warning btn-addon',
            show_modal: false,
            force_selected_row: true
        },
        DELETE: {
            visible: true,
            caption: 'Xóa',
            icon: 'fa fa-trash-o',
            btn_class: 'btn m-b-xs btn-sm btn-danger btn-addon',
            show_modal: true,
            force_selected_row: true
        }
    };
    @Input() action_master: any = {
        ADD: {
            visible: false,
            caption: 'Thêm',
            icon: 'fa fa-plus',
            btn_class: 'btn m-b-xs btn-sm btn-primary btn-addon',
            show_modal: false,
            force_selected_row: false
        },
        EDIT: {
            visible: false,
            caption: 'Cập nhật',
            icon: 'fa fa-pencil',
            btn_class: 'btn m-b-xs btn-sm btn-warning btn-addon',
            show_modal: false,
            force_selected_row: true
        },
        DELETE: {
            visible: false,
            caption: 'Xóa',
            icon: 'fa fa-trash-o',
            btn_class: 'btn m-b-xs btn-sm btn-danger btn-addon',
            show_modal: true,
            force_selected_row: true
        }
    };
    @Input() settingClassDetail: string = 'active';

    // Data Detail
    private _detail: any[] = [];
    @Input()
    get detail(): any[] {
        return this._detail;
    }
    @Output() detailChange = new EventEmitter();
    set detail(val: any[]) {
        this._detail = val;
        this.detailChange.emit(this._detail);
    }

    // Data Master
    @Input() get master(): any[] {
        return this.master_data;
    }

    set master(obj: any[]) {
        this.master_data = obj;
        if (this.master_data.length > 0)
            this.setPage(1);
    }

    /** ===== OUTPUT ===== **/
    @Output() onClickedMaster: EventEmitter<any> = new EventEmitter();

    public clickedMaster(mode: string): void {
        let index = this.selectedRowMaster + (this.pager.currentPage * this.pageSize) - this.pageSize;
        let data = this.master[index];
        if (typeof data !== 'undefined') {
            this.onClickedMaster.emit({index: index, mode: mode, data: data});

            if(this.action_master[mode].show_modal)
                this.domHelperService.toggleModal('modal-confirm');
        }
        else {
            if(this.action_master[mode].force_selected_row)
                this.toastrHelperService.showToastr('warning', 'Vui lòng chọn một dòng dữ liệu!');
            else
                this.onClickedMaster.emit({index: 0, mode: mode, data: {}});
        }
    }

    @Output() onClickedDetail: EventEmitter<any> = new EventEmitter();

    public clickedDetail(mode: string): void {
        switch (this.settingClassDetail) {
            case 'highlight':
                let data_detail = this.highlightRowsDetail.map(function(o){
                    return this.detail[o];
                }, this);

                this.onClickedDetail.emit({highlight_rows: this.highlightRowsDetail, mode: mode, data: data_detail});
                break;
            case 'active':
                let index = this.selectedRowDetail;
                let data = this.detail[index];
                if (typeof data !== 'undefined') {
                    this.onClickedDetail.emit({index: index, mode: mode, data: data});

                    if(this.action_detail[mode].show_modal)
                        this.domHelperService.getElementById('btn-show-modal').click();
                }
                else {
                    if(this.action_detail[mode].force_selected_row)
                        this.toastrHelperService.showToastr('warning', 'Vui lòng chọn một dòng dữ liệu!');
                    else
                        this.onClickedDetail.emit({index: 0, mode: mode, data: {}});
                }
                break;
            default:
                break;
        }
    }

    /** ===== FUNCTION ACTION ===== **/
    public showDetail(id: number): void {
        if (this.dropdownRow == id) {
            this.dropdownRow = 0;
            return;
        }
        this.httpClientService.get(`${this.setup.link}/${id}`).subscribe(
            (success: any) => {
                if(success.hasOwnProperty('header_detail')) {
                    this.header_detail = success.header_detail;
                }

                this.detail = success[this.setup.json_name];
                this.dropdownRow = id;

                this.clearSelectedAndHighlightRowsDetail();
                this.setSortPropDetail();
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    public visible(key): boolean {
        return 'visible' in this.header_master[key] ? this.header_master[key]['visible'] : true;
    }

    /** ===== SELECTED & HIGHLIGHT ROW ===== **/
    public activeRowMaster(index: number): boolean {
        return index == this.selectedRowMaster;
    }

    public activeRowDetail(index: number): boolean {
        if (this.settingClassDetail == 'highlight') return false;
        return index == this.selectedRowDetail;
    }

    public highlightRowDetail(index: number): boolean {
        if (this.settingClassDetail == 'active') return false;
        let index_in_detail_data: number = index;
        let found = this.highlightRowsDetail.find(o => o == index_in_detail_data);
        return (typeof found !== "undefined");
    }

    public changeSettingClassDetail() {
        this.settingClassDetail = (this.settingClassDetail == 'active') ? 'highlight' : 'active';

        this.clearSelectedAndHighlightRowsDetail();
    }

    private clearSelectedAndHighlightRowsDetail(): void {
        this.selectedRowDetail = 0;
        this.highlightRowsDetail = [];
    }

    /** ===== SORT ===== **/
    public sortIndexMaster(mode: string): void {
        this.isAsc = !this.isAsc;
        this.pagedItems.reverse();
        this.master.reverse();
    }

    public sortPropNameMaster(data_type: string, sort: string, key: string): void {
        let prop_name = ('prop_name' in this.header_master[key]) ? this.header_master[key].prop_name : key;
        let isDesc: number = 0;
        let isAsc: number = 0;
        switch (sort) {
            case 'DESC':
                isDesc = -1;
                isAsc = 1;
                this.header_master[key].isDesc = false;
                this.header_master[key].isAsc = true;
                break;
            case 'ASC':
                isDesc = 1;
                isAsc = -1;
                this.header_master[key].isDesc = true;
                this.header_master[key].isAsc = false;
                break;
            default:
                break;
        }

        switch (data_type) {
            case 'TEXT':
                this.pagedItems.sort(function (left_side, right_side): number {
                    let prop_left_side: string = left_side[prop_name].toUpperCase();
                    let prop_right_side: string = right_side[prop_name].toUpperCase();
                    return (prop_left_side < prop_right_side) ? isDesc : (prop_left_side > prop_right_side) ? isAsc : 0;
                });
                this.master.sort(function (left_side, right_side): number {
                    let prop_left_side: string = left_side[prop_name].toUpperCase();
                    let prop_right_side: string = right_side[prop_name].toUpperCase();
                    return (prop_left_side < prop_right_side) ? isDesc : (prop_left_side > prop_right_side) ? isAsc : 0;
                });
                break;
            case 'NUMBER':
                this.pagedItems.sort(function (left_side, right_side): number {
                    let prop_left_side: number = Number(left_side[prop_name]);
                    let prop_right_side: number = Number(right_side[prop_name]);
                    return (prop_left_side < prop_right_side) ? isDesc : (prop_left_side > prop_right_side) ? isAsc : 0;
                });
                this.master.sort(function (left_side, right_side): number {
                    let prop_left_side: number = Number(left_side[prop_name]);
                    let prop_right_side: number = Number(right_side[prop_name]);
                    return (prop_left_side < prop_right_side) ? isDesc : (prop_left_side > prop_right_side) ? isAsc : 0;
                });
                break;
            case 'DATETIME':
                this.pagedItems.sort(function (left_side, right_side): number {
                    let sort_o1_before_o2: any = this.dateHelperService.isBefore(left_side[prop_name], 'YYYY-MM-DD HH:mm:ss', right_side[prop_name], 'YYYY-MM-DD HH:mm:ss');
                    let sort_o1_after_o2: any = this.dateHelperService.isAfter(left_side[prop_name], 'YYYY-MM-DD HH:mm:ss', right_side[prop_name], 'YYYY-MM-DD HH:mm:ss');
                    return sort_o1_before_o2 ? isDesc : sort_o1_after_o2 ? isAsc : 0;
                }.bind(this));
                this.master.sort(function (left_side, right_side): number {
                    let sort_o1_before_o2: any = this.dateHelperService.isBefore(left_side[prop_name], 'YYYY-MM-DD HH:mm:ss', right_side[prop_name], 'YYYY-MM-DD HH:mm:ss');
                    let sort_o1_after_o2: any = this.dateHelperService.isAfter(left_side[prop_name], 'YYYY-MM-DD HH:mm:ss', right_side[prop_name], 'YYYY-MM-DD HH:mm:ss');
                    return sort_o1_before_o2 ? isDesc : sort_o1_after_o2 ? isAsc : 0;
                }.bind(this));
                break;
            default:
                break;
        }
    }

    public sortPropNameDetail(data_type: string, sort: string, key: string): void {
        let prop_name = ('prop_name' in this.header_detail[key]) ? this.header_detail[key].prop_name : key;
        let isDesc: number = 0;
        let isAsc: number = 0;
        switch (sort) {
            case 'DESC':
                isDesc = -1;
                isAsc = 1;
                this.header_detail[key].isDesc = false;
                this.header_detail[key].isAsc = true;
                break;
            case 'ASC':
                isDesc = 1;
                isAsc = -1;
                this.header_detail[key].isDesc = true;
                this.header_detail[key].isAsc = false;
                break;
            default:
                break;
        }

        switch (data_type) {
            case 'TEXT':
                this.detail.sort(function (left_side, right_side): number {
                    let prop_left_side: string = left_side[prop_name].toUpperCase();
                    let prop_right_side: string = right_side[prop_name].toUpperCase();
                    return (prop_left_side < prop_right_side) ? isDesc : (prop_left_side > prop_right_side) ? isAsc : 0;
                });
                break;
            case 'NUMBER':
                this.detail.sort(function (left_side, right_side): number {
                    let prop_left_side: number = Number(left_side[prop_name]);
                    let prop_right_side: number = Number(right_side[prop_name]);
                    return (prop_left_side < prop_right_side) ? isDesc : (prop_left_side > prop_right_side) ? isAsc : 0;
                });
                break;
            case 'DATETIME':
                this.detail.sort(function (left_side, right_side): number {
                    let sort_o1_before_o2: any = this.dateHelperService.isBefore(left_side[prop_name], 'YYYY-MM-DD HH:mm:ss', right_side[prop_name], 'YYYY-MM-DD HH:mm:ss');
                    let sort_o1_after_o2: any = this.dateHelperService.isAfter(left_side[prop_name], 'YYYY-MM-DD HH:mm:ss', right_side[prop_name], 'YYYY-MM-DD HH:mm:ss');
                    return sort_o1_before_o2 ? isDesc : sort_o1_after_o2 ? isAsc : 0;
                }.bind(this));
                break;
            default:
                break;
        }
    }

    private setSortPropMaster(): void {
        for (let key in this.header_master) {
            if (this.header_master.hasOwnProperty(key)) {
                this.header_master[key].isAsc = false;
                this.header_master[key].isDesc = true;
            }
        }
    }

    private setSortPropDetail(): void {
        for (let key in this.header_detail) {
            if (this.header_detail.hasOwnProperty(key)) {
                this.header_detail[key].isAsc = false;
                this.header_detail[key].isDesc = true;
            }
        }
    }

    /** ===== PAGINATION ===== **/
    public setPage(page: number): void {
        if (page < 1 || page > this.pager.totalPages) {
            return;
        }

        // get pager object from service
        this.pager = this.paginationHelperService.getPager(this.master.length, page, this.pageSize);

        // get current page of items
        this.pagedItems = this.master.slice(this.pager.startIndex, this.pager.endIndex + 1);
    }
}