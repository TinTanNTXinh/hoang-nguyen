import {Component, OnInit, Input, Output, EventEmitter} from '@angular/core';
import {NumberHelperService} from '../../services/helpers/number.helper';

@Component({
    selector: 'xnumber',
    templateUrl: 'xnumber.component.html'
})
export class XNumberComponent implements OnInit {

    /** Variables */
    private numberValue: number = 0;

    constructor(private numberHelperService: NumberHelperService) {
    }

    ngOnInit() {
    }

    @Input() readonly: boolean = false;

    @Input()
    get number(): number {
        return this.numberValue;
    }

    @Output() numberChange = new EventEmitter();

    set number(val: number) {
        this.numberValue = this.numberHelperService.convertToNumber(val.toString());
        this.numberChange.emit(this.numberValue);
    }

    @Output() onInputed: EventEmitter<number> = new EventEmitter();
    public inputed(event: any) {
        this.numberValue = this.numberHelperService.convertToNumber(event.toString());
        this.onInputed.emit(this.number);
    }
}