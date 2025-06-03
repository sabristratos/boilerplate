Organize content into separate panels within a single container. Easily switch between sections without leaving the page.

For full-page navigation, use the [navbar component ->](https://fluxui.dev/components/navbar)

```
<flux:tab.group>    <flux:tabs wire:model="tab">        <flux:tab name="profile">Profile</flux:tab>        <flux:tab name="account">Account</flux:tab>        <flux:tab name="billing">Billing</flux:tab>    </flux:tabs>    <flux:tab.panel name="profile">...</flux:tab.panel>    <flux:tab.panel name="account">...</flux:tab.panel>    <flux:tab.panel name="billing">...</flux:tab.panel></flux:tab.group>
```

## [With icons](https://fluxui.dev/components/tabs#with-icons)

Associate tab labels with icons to visually distinguish different sections.

```
<flux:tab.group>    <flux:tabs>        <flux:tab name="profile" icon="user">Profile</flux:tab>        <flux:tab name="account" icon="cog-6-tooth">Account</flux:tab>        <flux:tab name="billing" icon="banknotes">Billing</flux:tab>    </flux:tabs>    <flux:tab.panel name="profile">...</flux:tab.panel>    <flux:tab.panel name="account">...</flux:tab.panel>    <flux:tab.panel name="billing">...</flux:tab.panel></flux:tab.group>
```

## [Padded edges](https://fluxui.dev/components/tabs#padded-edges)

By default, the tabs will have no horizontal padding around the edges. If you want to add padding you can do by adding Tailwind utilities to the tabs and/or tab.panel components.

```
<flux:tabs class="px-4">    <flux:tab name="profile">Profile</flux:tab>    <flux:tab name="account">Account</flux:tab>    <flux:tab name="billing">Billing</flux:tab></flux:tabs>
```

## [Segmented tabs](https://fluxui.dev/components/tabs#segmented-tabs)

Tab through content with visually separated, button-like tabs. Ideal for toggling between views inside a container with a constrained width.

```
<flux:tabs variant="segmented">    <flux:tab>List</flux:tab>    <flux:tab>Board</flux:tab>    <flux:tab>Timeline</flux:tab></flux:tabs>
```

## [Segmented with icons](https://fluxui.dev/components/tabs#segmented-with-icons)

Combine segmented tabs with icon prefixes.

```
<flux:tabs variant="segmented">    <flux:tab icon="list-bullet">List</flux:tab>    <flux:tab icon="squares-2x2">Board</flux:tab>    <flux:tab icon="calendar-days">Timeline</flux:tab></flux:tabs>
```

## [Small segmented tabs](https://fluxui.dev/components/tabs#small-segmented-tabs)

For more compact layouts, you can use the size="sm" prop to make the tabs smaller.

```
<flux:tabs variant="segmented" size="sm">    <flux:tab>Demo</flux:tab>    <flux:tab>Code</flux:tab></flux:tabs>
```

## [Pill tabs](https://fluxui.dev/components/tabs#pill-tabs)

Tab through content with visually separated, pill-like tabs.

```
<flux:tabs variant="pills">    <flux:tab>List</flux:tab>    <flux:tab>Board</flux:tab>    <flux:tab>Timeline</flux:tab></flux:tabs>
```

## [Dynamic tabs](https://fluxui.dev/components/tabs#dynamic-tabs)

If you need, you can dynamically generate additional tabs and panels in your Livewire component. Just make sure you use matching names for the new tabs and panels.

<flux:tab.group> <flux:tabs> @foreach($tabs as $id => $tab) <flux:tab :name="$id">{{ $tab }}</flux:tab> @endforeach <flux:tab icon="plus" wire:click="addTab" action>Add tab</flux:tab> </flux:tabs> @foreach($tabs as $id => $tab) <flux:tab.panel :name="$id"> <!-- ... --> </flux:tab.panel> @endforeach</flux:tab.group><!-- Livewire component example code... public array $tabs = [ 'tab-1' => 'Tab #1', 'tab-2' => 'Tab #2', ]; public function addTab(): void { $id = 'tab-' . str()->random(); $this->tabs[$id] = 'Tab #' . count($this->tabs) + 1; }-->

## Reference

### [flux:tab.group](https://fluxui.dev/components/tabs#fluxtabgroup)

Container for tabs and their associated panels.

|Slot|Description|
|---|---|
|default|The tabs and panels components.|

### [flux:tabs](https://fluxui.dev/components/tabs#fluxtabs)

|Prop|Description|
|---|---|
|wire:model|Binds the active tab to a Livewire property. See [wire:model documentation](https://livewire.laravel.com/docs/wire-model)|
|variant|Visual style of the tabs. Options: default, segmented, pills.|
|size|Size of the tabs. Options: base (default), sm.|

|Slot|Description|
|---|---|
|default|The individual tab components.|

|Attribute|Description|
|---|---|
|data-flux-tabs|Applied to the root element for styling and identification.|

### [flux:tab](https://fluxui.dev/components/tabs#fluxtab)

|Prop|Description|
|---|---|
|name|Unique identifier for the tab, used to match with its panel.|
|icon|Name of the icon to display at the start of the tab.|
|icon:trailing|Name of the icon to display at the end of the tab.|
|icon:variant|Variant of the icon. Options: outline, solid, mini, micro.|
|action|Converts the tab to an action button (used for "Add tab" functionality).|
|accent|If true, applies accent color styling to the tab.|
|size|Size of the tab (only applies when variant="segmented"). Options: base (default), sm.|
|disabled|Disables the tab.|

|Slot|Description|
|---|---|
|default|The tab label content.|

|Attribute|Description|
|---|---|
|data-flux-tab|Applied to the tab element for styling and identification.|
|data-selected|Applied when the tab is selected/active.|

### [flux:tab.panel](https://fluxui.dev/components/tabs#fluxtabpanel)

|Prop|Description|
|---|---|
|name|Unique identifier matching the associated tab.|

|Slot|Description|
|---|---|
|default|The panel content displayed when the associated tab is selected.|

|Attribute|Description|
|---|---|
|data-flux-tab-panel|Applied to the panel element for styling and identification.|


