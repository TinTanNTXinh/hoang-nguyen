import {Component, HostBinding, Input, Output, EventEmitter, AfterViewInit} from '@angular/core';

@Component({
    selector: 'tag-input',
    templateUrl: './tag-input.component.html',
    styleUrls: ['./tag-input.component.css']
})
export class TagInputComponent implements AfterViewInit{

    private tagsList: string[] = [];

    @Input()
    get value() {
        return this.tagsList;
    }

    @Output() valueChange = new EventEmitter();

    set value(val) {
        this.tagsList = val;
        this.valueChange.emit(this.tagsList);
    }

    @Input() placeholder: string = 'Add a tag';
    @Input() delimiterCode: string = '188';
    @Input() addOnBlur: boolean = true;
    @Input() addOnEnter: boolean = true;
    @Input() addOnPaste: boolean = true;
    @Input() allowedTagsPattern: RegExp = /.+/;
    @HostBinding('class.ng2-tag-input-focus') isFocussed;

    public inputValue: string = '';
    public delimiter: number;
    public selectedTag: number;

    constructor() {
    }

    ngOnInit() {
        this.delimiter = parseInt(this.delimiterCode);
    }

    ngAfterViewInit() {
        // If the user passes an undefined variable to ngModel this will warn
        // and set the value to an empty array
        if (!this.value) {
            console.warn('TagInputComponent was passed an undefined value in ngModel. Please make sure the variable is defined.');
            this.value = [];
        }
    }

    public inputChanged(event) {
        let key = event.keyCode;
        switch (key) {
            case 8: // Backspace
                this._handleBackspace();
                break;
            case 13: //Enter
                this.addOnEnter && this._addTags([this.inputValue]);
                event.preventDefault();
                break;

            case this.delimiter:
                this._addTags([this.inputValue]);
                event.preventDefault();
                break;

            default:
                this._resetSelected();
                break;
        }
    }

    public inputBlurred(event) {
        this.addOnBlur && this._addTags([this.inputValue]);
        this.isFocussed = false;
    }

    public inputFocused(event) {
        this.isFocussed = true;
    }

    public inputPaste(event) {
        let clipboardData = event.clipboardData || (event.originalEvent && event.originalEvent.clipboardData);
        let pastedString = clipboardData.getData('text/plain');
        let tags = this._splitString(pastedString);
        let tagsToAdd = tags.filter((tag) => this._isTagValid(tag));
        this._addTags(tagsToAdd);
        setTimeout(() => this.inputValue = '', 3000);
    }

    private _splitString(tagString: string) {
        tagString = tagString.trim();
        let tags = tagString.split(String.fromCharCode(this.delimiter));
        return tags.filter((tag) => !!tag);
    }

    private _isTagValid(tagString: string) {
        return this.allowedTagsPattern.test(tagString);
    }

    private _addTags(tags: string[]) {
        let validTags = tags.filter((tag) => this._isTagValid(tag));
        let item = this.value.find(o => o == validTags[0]);

        // Check exist value
        if (item) {
            return;
        }

        this.value = this.value.concat(validTags);
        this._resetSelected();
        this._resetInput();
    }

    public _removeTag(tagIndexToRemove) {
        this.value.splice(tagIndexToRemove, 1);
        this._resetSelected();
    }

    private _handleBackspace() {
        if (!this.inputValue.length && this.value.length) {
            if (this.selectedTag != null) {
                this._removeTag(this.selectedTag);
            }
            else {
                this.selectedTag = this.value.length - 1;
            }
        }
    }

    private _resetSelected() {
        this.selectedTag = null;
    }

    private _resetInput() {
        this.inputValue = '';
    }
}
