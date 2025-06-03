Capture user data with various forms of text input.

```
<flux:field>    <flux:label>Username</flux:label>    <flux:description>This will be publicly displayed.</flux:description>    <flux:input />    <flux:error name="username" /></flux:field>
```

## [Shorthand](https://fluxui.dev/components/input#shorthand)

Because using the field component in its full form can be verbose and repetitive, all form controls in flux allow you pass a label and a description parameter directly. Under the hood, they will be wrapped in a field with an error component automatically.

```
<flux:input label="Username" description="This will be publicly displayed." />
```

## [Class targeting](https://fluxui.dev/components/input#class-targeting)

Unlike other form components, Flux's input component is composed of two underlying elements: an input element and a wrapper div. The wrapper div is there to add padding where icons should go.

This is typically fine, however, if you need to pass classes into the input component and have them directly applied to the input element, you can do so using the class:input attribute instead of simply class:

```
<flux:input class="max-w-xs" class:input="font-mono" />
```

## [Types](https://fluxui.dev/components/input#types)

Use the browser's various input types for different situations: text, email, password, etc.

<flux:input type="email" label="Email" />
<flux:input type="password" label="Password" />
<flux:input type="date" max="2999-12-31" label="Date" />

## [File](https://fluxui.dev/components/input#file)

Flux provides a special input component for file uploads. It's a simple wrapper around the native input[type="file"] element.

<flux:input type="file" wire:model="logo" label="Logo"/>
<flux:input type="file" wire:model="attachments" label="Attachments" multiple />

## [Smaller](https://fluxui.dev/components/input#smaller)

Use the size prop to make the input's height more compact.

<flux:input size="sm" placeholder="Filter by..." />

## [Disabled](https://fluxui.dev/components/input#disabled)

Prevent users from interacting with an input by disabling it.

```
<flux:input disabled label="Email" />
```

## [Readonly](https://fluxui.dev/components/input#readonly)

Useful for locking an input during a form submission.

```
<flux:input readonly variant="filled" />
```

## [Invalid](https://fluxui.dev/components/input#invalid)

Signal to users that the contents of an input are invalid.

```
<flux:input invalid />
```

## [Input masking](https://fluxui.dev/components/input#input-masking)

Restrict the formatting of text content for unique cases by using [Alpine's mask plugin](https://alpinejs.dev/plugins/mask)

```
<flux:input mask="(999) 999-9999" value="7161234567" />
```

## [Icons](https://fluxui.dev/components/input#icons)

Append or prepend an icon to the inside of a form input.

```
<flux:input icon="magnifying-glass" placeholder="Search orders" /><flux:input icon:trailing="credit-card" placeholder="4444-4444-4444-4444" /><flux:input icon:trailing="loading" placeholder="Search transactions" />
```

## [Icon buttons](https://fluxui.dev/components/input#icon-buttons)

Append a button to the inside of an input to provide associated functionality.

```
<flux:input placeholder="Search orders">    <x-slot name="iconTrailing">        <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" />    </x-slot></flux:input><flux:input type="password" value="password">    <x-slot name="iconTrailing">        <flux:button size="sm" variant="subtle" icon="eye" class="-mr-1" />    </x-slot></flux:input>
```

## [Clearable, copyable, and viewable inputs](https://fluxui.dev/components/input#clearable-copyable-and-viewable-inputs)

Flux provides three special input properties to configure common input button behaviors. clearable for clearing contents, copyable for copying contents, and viewable for toggling password visibility.

```
<flux:input placeholder="Search orders" clearable /><flux:input type="password" value="password" viewable /><flux:input icon="key" value="FLUX-1234-5678-ABCD-EFGH" readonly copyable />
```

## [Keyboard hint](https://fluxui.dev/components/input#keyboard-hint)

Hint to users what keyboard shortcuts they can use with this input.

```
<flux:input kbd="⌘K" icon="magnifying-glass" placeholder="Search..."/>
```

## [As a button](https://fluxui.dev/components/input#as-a-button)

To render an input using a button element, pass "button" into the as prop.

```
<flux:input as="button" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" />
```

## [With buttons](https://fluxui.dev/components/input#with-buttons)

Attach buttons to the beginning or end of an input element.

```
<flux:input.group>    <flux:input placeholder="Post title" />    <flux:button icon="plus">New post</flux:button></flux:input.group><flux:input.group>    <flux:select class="max-w-fit">        <flux:select.option selected>USD</flux:select.option>        <!-- ... -->    </flux:select>    <flux:input placeholder="$99.99" /></flux:input.group>
```

## [Text prefixes and suffixes](https://fluxui.dev/components/input#text-prefixes-and-suffixes)

Append text inside a form input.

```
<flux:input.group>    <flux:input.group.prefix>https://</flux:input.group.prefix>    <flux:input placeholder="example.com" /></flux:input.group><flux:input.group>    <flux:input placeholder="chunky-spaceship" />    <flux:input.group.suffix>.brand.com</flux:input.group.suffix></flux:input.group>
```

## [Input group labels](https://fluxui.dev/components/input#input-group-labels)

If you want to use an input group in a form field with a label, you will need to wrap the input group in a field component.

<flux:field> <flux:label>Website</flux:label> <flux:input.group> <flux:input.group.prefix>https://</flux:input.group.prefix> <flux:input wire:model="website" placeholder="example.com" /> </flux:input.group> <flux:error name="website" /></flux:field>

## Reference

### [flux:input](https://fluxui.dev/components/input#fluxinput)

|Prop|Description|
|---|---|
|wire:model|Binds the input to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|
|label|Label text displayed above the input. When provided, wraps the input in a flux:field component with an adjacent flux:label component. See the [field component](https://fluxui.dev/components/field).|
|description|Help text displayed above the input. When provided alongside label, appears between the label and input within the flux:field wrapper. See the [field component](https://fluxui.dev/components/field).|
|description:trailing|Help text displayed below the input. When provided alongside label, appears between the label and input within the flux:field wrapper. See the [field component](https://fluxui.dev/components/field).|
|placeholder|Placeholder text displayed when the input is empty.|
|size|Size of the input. Options: sm, xs.|
|variant|Visual style variant. Options: filled. Default: outline.|
|disabled|Prevents user interaction with the input.|
|readonly|Makes the input read-only.|
|invalid|Applies error styling to the input.|
|multiple|For file inputs, allows selecting multiple files.|
|mask|Input mask pattern using Alpine's mask plugin. Example: 99/99/9999.|
|icon|Name of the icon displayed at the start of the input.|
|icon:trailing|Name of the icon displayed at the end of the input.|
|kbd|Keyboard shortcut hint displayed at the end of the input.|
|clearable|If true, displays a clear button when the input has content.|
|copyable|If true, displays a copy button to copy the input's content.|
|viewable|For password inputs, displays a toggle to show/hide the password.|
|as|Render the input as a different element. Options: button. Default: input.|
|class:input|CSS classes applied directly to the input element instead of the wrapper.|

|Slot|Description|
|---|---|
|icon|Custom content displayed at the start of the input (e.g., icons).|
|icon:leading|Custom content displayed at the start of the input (e.g., icons).|
|icon:trailing|Custom content displayed at the end of the input (e.g., buttons).|

|Attribute|Description|
|---|---|
|data-flux-input|Applied to the root element for styling and identification.|

### [flux:input.group](https://fluxui.dev/components/input#fluxinputgroup)

|Slot|Description|
|---|---|
|default|The input group content, typically containing an input and prefix/suffix elements.|

### [flux:input.group.prefix](https://fluxui.dev/components/input#fluxinputgroupprefix)

|Slot|Description|
|---|---|
|default|Content displayed before the input (e.g., icons, text, buttons).|

### [flux:input.group.suffix](https://fluxui.dev/components/input#fluxinputgroupsuffix)

|Slot|Description|
|---|---|
|default|Content displayed after the input (e.g., icons, text, buttons).|

See the [field component documentation](https://fluxui.dev/components/field) for more information.