A composable dropdown component that can handle both simple navigation menus as well as complex action menus with checkboxes, radios, and submenus.

```
<flux:dropdown>    <flux:button icon:trailing="chevron-down">Options</flux:button>    <flux:menu>        <flux:menu.item icon="plus">New post</flux:menu.item>        <flux:menu.separator />        <flux:menu.submenu heading="Sort by">            <flux:menu.radio.group>                <flux:menu.radio checked>Name</flux:menu.radio>                <flux:menu.radio>Date</flux:menu.radio>                <flux:menu.radio>Popularity</flux:menu.radio>            </flux:menu.radio.group>        </flux:menu.submenu>        <flux:menu.submenu heading="Filter">            <flux:menu.checkbox checked>Draft</flux:menu.checkbox>            <flux:menu.checkbox checked>Published</flux:menu.checkbox>            <flux:menu.checkbox>Archived</flux:menu.checkbox>        </flux:menu.submenu>        <flux:menu.separator />        <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>    </flux:menu></flux:dropdown>
```

## [Navigation menus](https://fluxui.dev/components/dropdown#navigation-menus)

Display a simple set of links in a dropdown menu.

Use the navmenu component for a simple collections of links. Otherwise, use the menu component for action menus that require keyboard navigation, submenus, etc.

```
<flux:dropdown position="bottom" align="end">    <flux:profile avatar="/img/demo/user.png" name="Olivia Martin" />    <flux:navmenu>        <flux:navmenu.item href="#" icon="user">Account</flux:navmenu.item>        <flux:navmenu.item href="#" icon="building-storefront">Profile</flux:navmenu.item>        <flux:navmenu.item href="#" icon="credit-card">Billing</flux:navmenu.item>        <flux:navmenu.item href="#" icon="arrow-right-start-on-rectangle">Logout</flux:navmenu.item>        <flux:navmenu.item href="#" icon="trash" variant="danger">Delete</flux:navmenu.item>    </flux:navmenu></flux:dropdown>
```

## [Positioning](https://fluxui.dev/components/dropdown#positioning)

Customize the position of the dropdown menu via the position and align props. You can first pass the base position: top, bottom, left, and right, then an alignment modifier like start, center, or end.

```
<flux:dropdown position="top" align="start"><!-- More positions... --><flux:dropdown position="right" align="center"><flux:dropdown position="bottom" align="center"><flux:dropdown position="left" align="end">
```

## [Offset & gap](https://fluxui.dev/components/dropdown#offset-gap)

Customize the offset/gap of the dropdown menu via the offset and gap props. These properties accept values in pixels.

```
<flux:dropdown offset="-15" gap="2">
```

## [Keyboard hints](https://fluxui.dev/components/dropdown#keyboard-hints)

Add keyboard shortcut hints to menu items to teach users how to navigate your app faster.

```
<flux:dropdown>    <flux:button icon:trailing="chevron-down">Options</flux:button>    <flux:menu>        <flux:menu.item icon="pencil-square" kbd="⌘S">Save</flux:menu.item>        <flux:menu.item icon="document-duplicate" kbd="⌘D">Duplicate</flux:menu.item>        <flux:menu.item icon="trash" variant="danger" kbd="⌘⌫">Delete</flux:menu.item>    </flux:menu></flux:dropdown>
```

## [Checkbox items](https://fluxui.dev/components/dropdown#checkbox-items)

Select one or many menu options.

```
<flux:dropdown>    <flux:button icon:trailing="chevron-down">Permissions</flux:button>    <flux:menu>        <flux:menu.checkbox wire:model="read" checked>Read</flux:menu.checkbox>        <flux:menu.checkbox wire:model="write" checked>Write</flux:menu.checkbox>        <flux:menu.checkbox wire:model="delete">Delete</flux:menu.checkbox>    </flux:menu></flux:dropdown>
```

## [Radio items](https://fluxui.dev/components/dropdown#radio-items)

Select a single menu option.

```
<flux:dropdown>    <flux:button icon:trailing="chevron-down">Sort by</flux:button>    <flux:menu>        <flux:menu.radio.group wire:model="sortBy">            <flux:menu.radio checked>Latest activity</flux:menu.radio>            <flux:menu.radio>Date created</flux:menu.radio>            <flux:menu.radio>Most popular</flux:menu.radio>        </flux:menu.radio.group>    </flux:menu></flux:dropdown>
```

## [Groups](https://fluxui.dev/components/dropdown#groups)

Visually group related menu items with a separator line.

```
<flux:dropdown>    <flux:button icon:trailing="chevron-down">Options</flux:button>    <flux:menu>        <flux:menu.item>View</flux:menu.item>        <flux:menu.item>Transfer</flux:menu.item>        <flux:menu.separator />        <flux:menu.item>Publish</flux:menu.item>        <flux:menu.item>Share</flux:menu.item>        <flux:menu.separator />        <flux:menu.item variant="danger">Delete</flux:menu.item>    </flux:menu></flux:dropdown>
```

## [Groups with headings](https://fluxui.dev/components/dropdown#groups-with-headings)

Group options under headings to make them more discoverable.

```
<flux:dropdown>    <flux:button icon:trailing="chevron-down">Options</flux:button>    <flux:menu>        <flux:menu.group heading="Account">            <flux:menu.item>Profile</flux:menu.item>            <flux:menu.item>Permissions</flux:menu.item>        </flux:menu.group>        <flux:menu.group heading="Billing">            <flux:menu.item>Transactions</flux:menu.item>            <flux:menu.item>Payouts</flux:menu.item>            <flux:menu.item>Refunds</flux:menu.item>        </flux:menu.group>        <flux:menu.item>Logout</flux:menu.item>    </flux:menu></flux:dropdown>
```

## [Submenus](https://fluxui.dev/components/dropdown#submenus)

Nest submenus for more condensed menus.

<flux:dropdown> <flux:button icon:trailing="chevron-down">Options</flux:button> <flux:menu> <flux:menu.submenu heading="Sort by"> <flux:menu.radio checked>Name</flux:menu.radio> <flux:menu.radio>Date</flux:menu.radio> <flux:menu.radio>Popularity</flux:menu.radio> </flux:menu.submenu> <flux:menu.submenu heading="Filter"> <flux:menu.checkbox checked>Draft</flux:menu.checkbox> <flux:menu.checkbox checked>Published</flux:menu.checkbox> <flux:menu.checkbox>Archived</flux:menu.checkbox> </flux:menu.submenu> <flux:menu.separator /> <flux:menu.item variant="danger">Delete</flux:menu.item> </flux:menu></flux:dropdown>

## Reference

### [flux:dropdown](https://fluxui.dev/components/dropdown#fluxdropdown)

|Prop|Description|
|---|---|
|position|Position of the dropdown menu. Options: top, right, bottom (default), left.|
|align|Alignment of the dropdown menu. Options: start, center, end. Default: start.|
|offset|Offset in pixels from the trigger element. Default: 0.|
|gap|Gap in pixels between trigger and menu. Default: 4.|

|Attribute|Description|
|---|---|
|data-flux-dropdown|Applied to the root element for styling and identification.|

### [flux:menu](https://fluxui.dev/components/dropdown#fluxmenu)

A complex menu component that supports keyboard navigation, submenus, checkboxes, and radio buttons.

|Slot|Description|
|---|---|
|default|The menu items, separators, and submenus.|

|Attribute|Description|
|---|---|
|data-flux-menu|Applied to the root element for styling and identification.|

### [flux:menu.item](https://fluxui.dev/components/dropdown#fluxmenuitem)

|Prop|Description|
|---|---|
|icon|Name of the icon to display at the start of the item.|
|icon:trailing|Name of the icon to display at the end of the item.|
|icon:variant|Variant of the icon. Options: outline, solid, mini, micro.|
|kbd|Keyboard shortcut hint displayed at the end of the item.|
|suffix|Text displayed at the end of the item.|
|variant|Visual style of the item. Options: default, danger.|
|disabled|If true, prevents interaction with the menu item.|

|Attribute|Description|
|---|---|
|data-flux-menu-item|Applied to the root element for styling and identification.|
|data-active|Applied when the item is hovered/active.|

### [flux:menu.submenu](https://fluxui.dev/components/dropdown#fluxmenusubmenu)

|Prop|Description|
|---|---|
|heading|Text displayed as the submenu heading.|
|icon|Name of the icon to display at the start of the submenu.|
|icon:trailing|Name of the icon to display at the end of the submenu.|
|icon:variant|Variant of the icon. Options: outline, solid, mini, micro.|

|Slot|Description|
|---|---|
|default|The submenu items (checkboxes, radio buttons, etc.).|

### [flux:menu.separator](https://fluxui.dev/components/dropdown#fluxmenuseparator)

A horizontal line that separates menu items.

### [flux:menu.checkbox-group](https://fluxui.dev/components/dropdown#fluxmenucheckbox-group)

|Prop|Description|
|---|---|
|wire:model|Binds the checkbox group to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|

|Slot|Description|
|---|---|
|default|The checkboxes.|

### [flux:menu.checkbox](https://fluxui.dev/components/dropdown#fluxmenucheckbox)

|Prop|Description|
|---|---|
|wire:model|Binds the checkbox to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|
|checked|If true, the checkbox is checked by default.|
|disabled|If true, prevents interaction with the checkbox.|

|Attribute|Description|
|---|---|
|data-active|Applied when the checkbox is hovered/active.|
|data-checked|Applied when the checkbox is checked.|

### [flux:menu.radio.group](https://fluxui.dev/components/dropdown#fluxmenuradiogroup)

|Prop|Description|
|---|---|
|wire:model|Binds the radio group to a Livewire property. See the [wire:model documentation](https://livewire.laravel.com/docs/wire-model) for more information.|

|Slot|Description|
|---|---|
|default|The radio buttons.|

### [flux:menu.radio](https://fluxui.dev/components/dropdown#fluxmenuradio)

|Prop|Description|
|---|---|
|checked|If true, the radio button is selected by default.|
|disabled|If true, prevents interaction with the radio button.|

|Attribute|Description|
|---|---|
|data-active|Applied when the radio button is hovered/active.|
|data-checked|Applied when the radio button is selected.|