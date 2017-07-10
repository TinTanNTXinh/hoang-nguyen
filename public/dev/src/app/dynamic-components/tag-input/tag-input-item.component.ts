import {Component, EventEmitter, Input, Output} from '@angular/core';

@Component({
    selector: 'tag-input-item',
    templateUrl: './tag-input-item.component.html',
    styleUrls: ['./tag-input-item.component.css'],
    host: {
        '[class.ng2-tag-input-item-selected]': 'selected'
    }
})
export class TagInputItemComponent {
    @Input() selected: boolean;
    @Input() text: string;
    @Input() index: number;
    @Output() tagRemoved: EventEmitter<number> = new EventEmitter();

    constructor() {
    }

    public removeTag(): void {
        this.tagRemoved.emit(this.index);
    }
}
