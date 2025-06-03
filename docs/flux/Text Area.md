Capture multi-line text input from users. Ideal for comments, descriptions, and feedback.

<flux:textarea />

## [With placeholder](https://fluxui.dev/components/textarea#with-placeholder)

Display a hint inside the textarea to guide users on what to enter.

```
<flux:textarea    label="Order notes"    placeholder="No lettuce, tomato, or onion..."/>
```

## [Fixed row height](https://fluxui.dev/components/textarea#fixed-row-height)

Customize the height of the textarea by passing a rows prop.

```
<flux:textarea rows="2" label="Note" />
```

## [Auto-sizing textarea](https://fluxui.dev/components/textarea#auto-sizing-textarea)

Using CSS's new field-sizing property, the textarea will automatically adjust its height to fit the content by passing in the rows="auto" prop.

This feature is not available in all web browsers. Visit [caniuse.com](https://caniuse.com/?search=field-sizing) to see which browsers support this feature.

```
<flux:textarea rows="auto" />
```

## [Configure resize](https://fluxui.dev/components/textarea#configure-resize)

If you want to restrict the user from resizing the textarea, you can use the resize="none" prop.

<flux:textarea resize="vertical" /><flux:textarea resize="none" /><flux:textarea resize="horizontal" /><flux:textarea resize="both" />

## Reference

### [flux:textarea](https://fluxui.dev/components/textarea#fluxtextarea)

|Prop|Description|
|---|---|
|wire:model|Binds the textarea to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|
|placeholder|Placeholder text displayed when the textarea is empty.|
|label|Label text displayed above the textarea. When provided, wraps the textarea in a flux:field component with an adjacent flux:label component. See the [field component](https://fluxui.dev/components/field).|
|description|Help text displayed below the textarea. When provided alongside label, appears between the label and textarea within the flux:field wrapper. See the [field component](https://fluxui.dev/components/field).|
|description:trailing|The description provided will be displayed below the textarea instead of above it.|
|badge|Badge text displayed at the end of the flux:label component when the label prop is provided.|
|rows|Number of visible text lines. Use "auto" for automatic height adjustment. Default: 4.|
|resize|Control how the textarea can be resized. Options: vertical (default), horizontal, both, none.|
|invalid|If true, applies error styling to the textarea.|

|Attribute|Description|
|---|---|
|data-flux-textarea|Applied to the textarea element for styling and identification.|