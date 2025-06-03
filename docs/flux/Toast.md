Flux Pro component

This component is only available in the Pro version of Flux.

# Toast

A message that provides feedback to users about an action or event, often temporary and dismissible.

To use the Toast component from Livewire, you must include it somewhere on the page; often in your layout file:

```
<body>    <!-- ... -->    <flux:toast /></body>
```

If you are using wire:navigate to navigate between pages, you may want to persist the toast component so that toast messages don't suddenly disappear when navigating away from the page.

```
<body>    <!-- ... -->    @persist('toast')        <flux:toast />    @endpersist</body>
```

Once the toast component is present on the page, you can use the Flux::toast() method to trigger a toast message from your Livewire components:

Save changes

```
<?phpnamespace App\Livewire;use Livewire\Component;use Flux\Flux;class EditPost extends Component{    public function save()    {        // ...        Flux::toast('Your changes have been saved.');    }}
```

You can also trigger a toast from Alpine directly using Flux's magic methods:

```
<button x-on:click="$flux.toast('Your changes have been saved.')">    Save changes</button>
```

Or you can use the window.Flux global object to trigger a toast from any JavaScript in your application:

```
<script>    let button = document.querySelector('...')    button.addEventListener('alpine:init', () => {        Flux.toast('Your changes have been saved.')    })</script>
```

Both $flux and window.Flux support the following method parameter signatures:

```
Flux.toast('Your changes have been saved.')// Or...Flux.toast({    heading: 'Changes saved',    text: 'Your changes have been saved.',    variant: 'success',})
```

## [With heading](https://fluxui.dev/components/toast#with-heading)

Use a heading to provide additional context for the toast.

Save changes

```
Flux::toast(    heading: 'Changes saved.',    text: 'You can always update this in your settings.',);
```

## [Variants](https://fluxui.dev/components/toast#variants)

Use the variant prop to change the visual style of the toast.

SuccessWarningDanger

```
Flux::toast(variant: 'success', ...);Flux::toast(variant: 'warning', ...);Flux::toast(variant: 'danger', ...);
```

## [Positioning](https://fluxui.dev/components/toast#positioning)

By default, the toast will appear in the bottom right corner of the page. You can customize this position using the position prop.

Save changes

```
<flux:toast position="top right" /><!-- Customize top padding for things like navbars... --><flux:toast position="top right" class="pt-24" />
```

## [Duration](https://fluxui.dev/components/toast#duration)

By default, the toast will automatically dismiss after 5 seconds. You can customize this duration by passing a number of milliseconds to the duration prop.

Save changes

```
// 1 second...Flux::toast(duration: 1000, ...);
```

## [Permanent](https://fluxui.dev/components/toast#permanent)

Use a value of 0 as the duration prop to make the toast stay open indefinitely.

Save changes

```
// Show indefinitely...Flux::toast(duration: 0, ...);
```

## Related components

[

Callout

A flexible content container for alerts, messages, and notifications



](https://fluxui.dev/components/callout)[

Modal

Display temporary content in a modal dialog



](https://fluxui.dev/components/modal)

## Reference

### [flux:toast](https://fluxui.dev/components/toast#fluxtoast)

|Prop|Description|
|---|---|
|position|Position of the toast on the screen. Options: bottom right (default), bottom left, top right, top left.|
|duration|Duration in milliseconds before the toast auto-dismisses. Use 0 for permanent toasts. Default: 5000.|
|variant|Visual style of the toast. Options: success, warning, danger.|

### [Flux::toast()](https://fluxui.dev/components/toast#fluxtoast)

The PHP method used to trigger toasts from Livewire components.

|Parameter|Description|
|---|---|
|heading|Optional heading text for the toast.|
|text|Main content text of the toast.|
|variant|Visual style. Options: success, warning, danger.|
|duration|Duration in milliseconds. Use 0 for permanent toasts. Default: 5000.|

### [$flux.toast()](https://fluxui.dev/components/toast#fluxtoast)

The Alpine.js magic method used to trigger toasts from Alpine components. It can be used in two ways:

```
// Simple usage with just a message...$flux.toast('Your changes have been saved')// Advanced usage with full configuration...$flux.toast({    heading: 'Success!',    text: 'Your changes have been saved',    variant: 'success',    duration: 3000})
```

|Parameter|Description|
|---|---|
|message|A string containing the toast message. When using this simple form, the message becomes the toast's text content.|
|options|Alternatively, an object containing: - heading: Optional title text - text: Main message text - variant: Visual style (success, warning, danger) - duration: Display time in milliseconds|

