declare module 'Typeguard' {
    export function isUndefined(value: any): value is undefined;

    export function isChecked(input: any): boolean;
}
