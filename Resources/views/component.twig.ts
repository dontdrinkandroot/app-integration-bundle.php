import {Component, OnInit, OnDestroy} from "@angular/core";

@Component({
    templateUrl: './{{htmlFilename}}'
})
export class {{className}} implements OnInit, OnDestroy
{
    constructor()
    {
    }

    /**
     * @override
     */
    public ngOnInit(): void
    {
    }

    /**
     * @override
     */
    public ngOnDestroy(): void
    {
    }
}
