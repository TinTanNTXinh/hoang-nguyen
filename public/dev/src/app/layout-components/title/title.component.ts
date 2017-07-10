import {Component, OnInit, Input} from '@angular/core';

@Component({
    selector: 'app-title',
    templateUrl: './title.component.html'
})
export class TitleComponent implements OnInit {

    constructor() {

    }

    ngOnInit() {
    }

    @Input() title: string = '';

}
