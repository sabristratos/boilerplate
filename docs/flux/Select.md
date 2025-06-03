Choose a single option from a dropdown list.

For lists of up to 5 items, consider using [checkboxes](https://fluxui.dev/components/checkbox) or [radio buttons](https://fluxui.dev/components/radio) instead.

```
<flux:select wire:model="industry" placeholder="Choose industry...">    <flux:select.option>Photography</flux:select.option>    <flux:select.option>Design services</flux:select.option>    <flux:select.option>Web development</flux:select.option>    <flux:select.option>Accounting</flux:select.option>    <flux:select.option>Legal services</flux:select.option>    <flux:select.option>Consulting</flux:select.option>    <flux:select.option>Other</flux:select.option></flux:select>
```

## [Small](https://fluxui.dev/components/select#small)

A smaller select element for more compact layouts.

```
<flux:select size="sm" placeholder="Choose industry...">    <flux:select.option>Photography</flux:select.option>    <flux:select.option>Design services</flux:select.option>    <flux:select.option>Web development</flux:select.option>    <flux:select.option>Accounting</flux:select.option>    <flux:select.option>Legal services</flux:select.option>    <flux:select.option>Consulting</flux:select.option>    <flux:select.option>Other</flux:select.option></flux:select>
```

## [Custom select](https://fluxui.dev/components/select#custom-select)

An alternative to the browser's native select element. Typically used when you need custom option styling like icons, images, and other treatments.

This variant is only available in the Pro version of Flux.


```
<flux:select variant="listbox" placeholder="Choose industry...">    <flux:select.option>Photography</flux:select.option>    <flux:select.option>Design services</flux:select.option>    <flux:select.option>Web development</flux:select.option>    <flux:select.option>Accounting</flux:select.option>    <flux:select.option>Legal services</flux:select.option>    <flux:select.option>Consulting</flux:select.option>    <flux:select.option>Other</flux:select.option></flux:select>
```

## The button slot

If you need full control over the button used to trigger the custom select, you can use the button slot to render it yourself.

```
<flux:select variant="listbox">    <x-slot name="button">        <flux:select.button class="rounded-full!" placeholder="Choose industry..." :invalid="$errors->has('...')" />    </x-slot>    <flux:select.option>Photography</flux:select.option>    ...</flux:select>
```

## Clearable

If you want to make the selected value clearable, you can use the clearable prop to add an "x" button to the right side of the input:

```
<flux:select variant="listbox" clearable>    ...</flux:select>
```

## Options with images/icons

One distinct advantage of using a custom listbox select over the native <select> element is that you can now add icons and images to your options.

```
<flux:select variant="listbox" placeholder="Select role...">    <flux:select.option>        <div class="flex items-center gap-2">            <flux:icon.shield-check variant="mini" class="text-zinc-400" /> Owner        </div>    </flux:select.option>    <flux:select.option>        <div class="flex items-center gap-2">            <flux:icon.key variant="mini" class="text-zinc-400" /> Administrator        </div>    </flux:select.option>    <flux:select.option>        <div class="flex items-center gap-2">            <flux:icon.user variant="mini" class="text-zinc-400" /> Member        </div>    </flux:select.option>    <flux:select.option>        <div class="flex items-center gap-2">            <flux:icon.eye variant="mini" class="text-zinc-400" /> Viewer        </div>    </flux:select.option></flux:select>
```

## [Searchable select](https://fluxui.dev/components/select#searchable-select)

The searchable select variant makes navigating large option lists easier for your users.

This variant is only available in the Pro version of Flux.

```
<flux:select variant="listbox" searchable placeholder="Choose industries...">    <flux:select.option>Photography</flux:select.option>    <flux:select.option>Design services</flux:select.option>    <flux:select.option>Web development</flux:select.option>    <flux:select.option>Accounting</flux:select.option>    <flux:select.option>Legal services</flux:select.option>    <flux:select.option>Consulting</flux:select.option>    <flux:select.option>Other</flux:select.option></flux:select>
```

## The search slot

If you need full control over the search field inside the listbox, you can use the search slot to render it yourself.

```
<flux:select variant="listbox" searchable>    <x-slot name="search">        <flux:select.search class="px-4" placeholder="Search industries..." />    </x-slot>    ...</flux:select>
```

## [Multiple select](https://fluxui.dev/components/select#multiple-select)

Allow your users to select multiple options from a list of options.

This variant is only available in the Pro version of Flux.

```
<flux:select variant="listbox" multiple placeholder="Choose industries...">    <flux:select.option>Photography</flux:select.option>    <flux:select.option>Design services</flux:select.option>    <flux:select.option>Web development</flux:select.option>    <flux:select.option>Accounting</flux:select.option>    <flux:select.option>Legal services</flux:select.option>    <flux:select.option>Consulting</flux:select.option>    <flux:select.option>Other</flux:select.option></flux:select>
```

## Selected suffix

By default, when more than one option is selected, the suffix " selected" will be appended to the number of selected options. You can customize this language by passing a selected-suffix prop to the select component.

```
<flux:select variant="listbox" selected-suffix="industries selected" multiple>    ...</flux:select>
```

If you pass a custom suffix, and need localization, you can use the __() helper function to translate the suffix:

```
<flux:select variant="listbox" selected-suffix="{{ __('industries selected') }}" multiple>    ...</flux:select>
```

## Checkbox indicator

If you prefer a checkbox indicator instead of the default checkmark icon, you can use the indicator="checkbox" prop.

```
<flux:select variant="listbox" indicator="checkbox" multiple>    ...</flux:select>
```

## Clearing search

By default, a searchable select will clear the search input when the user selects an option. If you want to disable this behavior, you can use the clear="close" prop to only clear the search input when the user closes the select.

```
<flux:select variant="listbox" searchable multiple clear="close">    ...</flux:select>
```

## [Combobox](https://fluxui.dev/components/select#combobox)

A versatile combobox that can be used for anything from basic autocomplete to complex multi-selects.

This variant is only available in the Pro version of Flux.

```
<flux:select variant="combobox" placeholder="Choose industry...">    <flux:select.option>Photography</flux:select.option>    <flux:select.option>Design services</flux:select.option>    <flux:select.option>Web development</flux:select.option>    <flux:select.option>Accounting</flux:select.option>    <flux:select.option>Legal services</flux:select.option>    <flux:select.option>Consulting</flux:select.option>    <flux:select.option>Other</flux:select.option></flux:select>
```

## The input slot

If you need full control over the input element used to trigger the combobox, you can use the input slot to render it yourself.

```
<flux:select variant="combobox">    <x-slot name="input">        <flux:select.input x-model="search" :invalid="$errors->has('...')" />    </x-slot>    ...</flux:select>
```

## [Dynamic options](https://fluxui.dev/components/select#dynamic-options)

If you want to dynamically generate options on the server, you can use the :filter="false" prop to disable client-side filtering.

This variant is only available in the Pro version of Flux.

```
<flux:select wire:model="userId" variant="combobox" :filter="false">    <x-slot name="input">        <flux:select.input wire:model.live="search" />    </x-slot>    @foreach ($this->users as $user)        <flux:select.option value="{{ $user->id }}" wire:key="{{ $user->id }}">            {{ $user->name }}        </flux:select.option>    @endforeach</flux:select><!--public $search = '';public $userId = null;#[\Livewire\Attributes\Computed]public function users() {    return \App\Models\User::query()        ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))        ->limit(20)        ->get();}-->
```

## Related components

[

Field

Structured form field with label and validation states



](https://fluxui.dev/components/field)[

Autocomplete

Enhanced text input with predictive suggestions



](https://fluxui.dev/components/autocomplete)[

Radio

Control for selecting one option from many



](https://fluxui.dev/components/radio)

## Reference

### [flux:select](https://fluxui.dev/components/select#fluxselect)

|Prop|Description|
|---|---|
|wire:model|Binds the select to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|
|placeholder|Text displayed when no option is selected.|
|label|Label text displayed above the select. When provided, wraps the select in a flux:field component with an adjacent flux:label component. See the [field component](https://fluxui.dev/components/field).|
|description|Help text displayed below the select. When provided alongside label, appears between the label and select within the flux:field wrapper. See the [field component](https://fluxui.dev/components/field).|
|description:trailing|The description provided will be displayed below the select instead of above it.|
|badge|Badge text displayed at the end of the flux:label component when the label prop is provided.|
|size|Size of the select. Options: sm, xs.|
|variant|Visual style of the select. Options: default (native select), listbox, combobox.|
|multiple|Allows selecting multiple options (listbox and combobox variants only).|
|filter|If false, disables client-side filtering.|
|searchable|Adds a search input to filter options (listbox and combobox variants only).|
|clearable|Displays a clear button when an option is selected (listbox and combobox variants only).|
|selected-suffix|Text appended to the number of selected options in multiple mode (listbox variant only).|
|clear|When to clear the search input. Options: select (default), close (listbox and combobox variants only).|
|disabled|Prevents user interaction with the select.|
|invalid|Applies error styling to the select.|

|Slot|Description|
|---|---|
|default|The select options.|
|trigger|Custom trigger content. Typically the select.button or select.input component (listbox and combobox variants only).|

|Attribute|Description|
|---|---|
|data-flux-select|Applied to the root element for styling and identification.|

### [flux:select.option](https://fluxui.dev/components/select#fluxselectoption)

|Prop|Description|
|---|---|
|value|Value associated with the option.|
|disabled|Prevents selecting the option.|

|Slot|Description|
|---|---|
|default|The option content (can include icons, images, etc. in listbox variant).|

### [flux:select.button](https://fluxui.dev/components/select#fluxselectbutton)

|Prop|Description|
|---|---|
|placeholder|Text displayed when no option is selected.|
|invalid|Applies error styling to the button.|
|size|Size of the button. Options: sm, xs.|
|disabled|Prevents selecting the option.|
|clearable|Displays a clear button when an option is selected.|

### [flux:select.input](https://fluxui.dev/components/select#fluxselectinput)

|Prop|Description|
|---|---|
|placeholder|Text displayed when no option is selected.|
|invalid|Applies error styling to the input.|
|size|Size of the input. Options: sm, xs.|

### [flux:select.search](https://fluxui.dev/components/select#fluxselectsearch)

|Prop|Description|
|---|---|
|placeholder|Placeholder text displayed when the input is empty.|
|icon|Name of the icon displayed at the start of the input.|
|clearable|Displays a clear button when the input has content. Default: true.|
