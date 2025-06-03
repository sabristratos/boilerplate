Toggle a setting on or off. Suitable for binary options like enabling or disabling features.

Use switches as auto-saving controls outside forms; checkboxes otherwise.

```
<flux:field variant="inline">    <flux:label>Enable notifications</flux:label>    <flux:switch wire:model.live="notifications" />    <flux:error name="notifications" /></flux:field>
```

## [Fieldset](https://fluxui.dev/components/switch#fieldset)

Group related switches within a fieldset.

```
<flux:fieldset>    <flux:legend>Email notifications</flux:legend>    <div class="space-y-4">        <flux:switch wire:model.live="communication" label="Communication emails" description="Receive emails about your account activity." />        <flux:separator variant="subtle" />        <flux:switch wire:model.live="marketing" label="Marketing emails" description="Receive emails about new products, features, and more." />        <flux:separator variant="subtle" />        <flux:switch wire:model.live="social" label="Social emails" description="Receive emails for friend requests, follows, and more." />        <flux:separator variant="subtle" />        <flux:switch wire:model.live="security" label="Security emails" description="Receive emails about your account activity and security." />    </div></flux:fieldset>
```

## [Left align](https://fluxui.dev/components/switch#left-align)

Left align switches for more compact layouts using the align prop.

<flux:fieldset> <flux:legend>Email notifications</flux:legend> <div class="space-y-3"> <flux:switch label="Communication emails" align="left" /> <flux:switch label="Marketing emails" align="left" /> <flux:switch label="Social emails" align="left" /> <flux:switch label="Security emails" align="left" /> </div></flux:fieldset>

## Reference

### [flux:switch](https://fluxui.dev/components/switch#fluxswitch)

|Prop|Description|
|---|---|
|wire:model|Binds the switch to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|
|label|Label text displayed above the switch. When provided, wraps the switch in a flux:field component with an adjacent flux:label component. See the [field component](https://fluxui.dev/components/field).|
|description|Help text displayed below the switch. When provided alongside label, appears between the label and switch within the flux:field wrapper. See the [field component](https://fluxui.dev/components/field).|
|align|Alignment of the switch relative to its label. Options: right\|start (default), left\|end.|
|disabled|Prevents user interaction with the switch.|

|Attribute|Description|
|---|---|
|data-flux-switch|Applied to the root element for styling and identification.|
|data-checked|Applied when the switch is in the "on" state.|