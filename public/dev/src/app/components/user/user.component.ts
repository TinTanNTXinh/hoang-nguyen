import {Component, OnInit} from '@angular/core';

import {HttpClientService} from '../../services/httpClient.service';
import {DateHelperService} from '../../services/helpers/date.helper';
import {ToastrHelperService} from '../../services/helpers/toastr.helper';
import {DomHelperService} from '../../services/helpers/dom.helper';
import {ArrayHelperService} from '../../services/helpers/array.helper';

@Component({
    selector: 'app-user',
    templateUrl: './user.component.html',
    styles: []
})
export class UserComponent implements OnInit
    , ICommon, ICrud, IDatePicker, ISearch {

    /** My Variables **/
    public positions: any[] = [];
    public roles: any[] = [];
    public group_roles: any[] = [];
    public users: any[] = [];
    public users_search: any[] = [];
    public user: any;
    public birthday: Date;
    public fake_pwd: string = '';
    public fields: string[] = [
        'Doanh thu',
        'Lợi nhuận',
        'HĐ khống'
    ];
    public ngModelOptions = {standalone: true};

    /** ICommon **/
    title: string;
    placeholder_code: string;
    prefix_url: string;
    isLoading: boolean;
    header: any;
    action_data: any;

    /** ICrud **/
    modal: any;
    isEdit: boolean;

    /** IDatePicker **/
    range_date: any[];
    datepickerSettings: any;
    timepickerSettings: any;
    datepicker_from: Date;
    datepicker_to: Date;
    datepickerToOpts: any = {};

    /** ISearch **/
    filtering: any;

    constructor(private httpClientService: HttpClientService
        , private dateHelperService: DateHelperService
        , private toastrHelperService: ToastrHelperService
        , private domHelperService: DomHelperService
        , private arrayHelperService: ArrayHelperService) {
    }

    ngOnInit(): void {
        this.title = 'Người dùng';
        this.prefix_url = 'users';
        this.range_date = this.dateHelperService.range_date;

        this.datepickerSettings = this.dateHelperService.datepickerSettings;
        this.timepickerSettings = this.dateHelperService.timepickerSettings;
        this.header = {
            fullname: {
                title: 'Tên'
            },
            username: {
                title: 'Tài khoản'
            },
            address: {
                title: 'Địa chỉ'
            },
            phone: {
                title: 'Điện thoại'
            },
            sex: {
                title: 'Giới tính'
            },
            email: {
                title: 'Email'
            },
            note: {
                title: 'Ghi chú'
            }
        };
        this.modal = {
            id: 0,
            header: '',
            body: '',
            footer: ''
        };

        this.refreshData();

        this.clearValuePositions();
        this.clearValueRoles();
    }

    /** ICommon **/
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
        this.fake_pwd = arr_data['fake_pwd'];

        this.users = [];
        this.users_search = arr_data['users'];

        this.positions = arr_data['positions'];
        this.group_roles = arr_data['group_roles'];
        this.roles = arr_data['roles'];
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

    /** ICrud **/
    loadOne(id: number): void {
        this.httpClientService.get(`${this.prefix_url}/${id}`).subscribe(
            (success: any) => {
                // set user
                this.user = success['user'];

                // set birthday
                this.birthday = new Date(this.user.birthday);

                // set fake_pwd
                this.user.password = this.fake_pwd;

                // set user_roles
                this.clearValueRoles();
                success['user_roles'].forEach(function (role_id) {
                    let role = this.roles.find(o => o.id == role_id);
                    if (role)
                        role.value = true;
                }, this);

                // set user_positions
                this.clearValuePositions();
                success['user_positions'].forEach(function (position_id) {
                    let position = this.positions.find(o => o.id == position_id);
                    if (position)
                        position.value = true;
                }, this);
            },
            (error: any) => {
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    clearOne(): void {
        this.user = {
            fullname: '',
            username: '',
            password: '',
            address: '',
            phone: '',
            birthday: '',
            sex: 'Nam',
            email: '',
            note: ''
        };
        this.birthday = null;
    }

    addOne(): void {
        if (!this.validateOne()) return;

        this.user.birthday = this.dateHelperService.getDate(this.birthday);

        let data: { user: any, user_roles: number[], user_positions: number[] } = {
            "user": this.user,
            "user_roles": this.roles.filter(o => o.value == true).map(obj => obj.id),
            "user_positions": this.positions.filter(o => o.value == true).map(obj => obj.id)
        };

        this.httpClientService.post(this.prefix_url, {"user": data}).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.clearOne();
                this.toastrHelperService.showToastr('success', 'Thêm thành công.');
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

        this.user.birthday = this.dateHelperService.getDate(this.birthday);

        let data: { user: any, user_roles: number[], user_positions: number[] } = {
            "user": this.user,
            "user_roles": this.roles.filter(o => o.value == true).map(obj => obj.id),
            "user_positions": this.positions.filter(o => o.value == true).map(obj => obj.id)
        };

        this.httpClientService.put(this.prefix_url, {"user": data}).subscribe(
            (success: any) => {
                this.reloadData(success);
                this.clearOne();
                this.displayEditBtn(false);
                this.toastrHelperService.showToastr('success', 'Cập nhật thành công.');
            },
            (error: any) => {
                for (let err of error.json()['msg']) {
                    this.toastrHelperService.showToastr('error', err);
                }
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
                this.toastrHelperService.showToastr('error');
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
                this.toastrHelperService.showToastr('error');
            }
        );
    }

    confirmDeactivateOne(id: number): void {
        this.deactivateOne(id);
    }

    validateOne(): boolean {
        let flag: boolean = true;
        if (this.user.fullname == '') {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Họ tên ${this.title} không được để trống.`);
        }
        if (this.user.username == '') {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Tài khoản ${this.title} không được để trống.`);
        }
        if (this.user.password == '') {
            flag = false;
            this.toastrHelperService.showToastr('warning', `Mật khẩu ${this.title} không được để trống.`);
        }
        if (this.birthday == null) {
            flag = false;
            this.toastrHelperService.showToastr('warning', 'Ngày sinh không được để trống.');
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

    /** IDatePicker **/
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

    /** ISearch **/
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
        this.users = arr_data['users'];
    }

    clearSearch(): void {
        this.datepicker_from = null;
        this.datepicker_to = null;
        this.filtering = {
            from_date: '',
            to_date: '',
            range: '',
            user_id: 0,
        };
    }

    displayColumn(): void {
        let setting = {
            position_id: ['position_name'],
            username: ['username'],
            fullname: ['fullname'],
            phone: ['phone'],
        };
        for (let parent in setting) {
            for (let child of setting[parent]) {
                if (!!this.header[child])
                    this.header[child].visible = !(!!this.filtering[parent]);
            }
        }
    }

    /** My Function **/
    public slideRoles(group_role_id): any[] {
        return this.roles.filter(o => o.group_role_id == group_role_id);
    }

    public chunk(data: any[], size) {
        return this.arrayHelperService.chunkArray(data, size);
    }

    private clearValuePositions(): void {
        this.positions.map(function (position) {
            position.value = false;
        });
    }

    private clearValueRoles(): void {
        this.roles.map(function (role) {
            role.value = false;
        });
    }
}
