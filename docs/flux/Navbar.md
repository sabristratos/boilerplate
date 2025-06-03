# Navbar

Arrange navigation links vertically or horizontally.

Discover more about the navbar on the [layout documentation ->](https://fluxui.dev/layouts/sidebar)

[

Home

](https://fluxui.dev/components/navbar#)[

Features

](https://fluxui.dev/components/navbar#)[

Pricing

](https://fluxui.dev/components/navbar#)[

About

](https://fluxui.dev/components/navbar#)

```
<flux:navbar>    <flux:navbar.item href="#">Home</flux:navbar.item>    <flux:navbar.item href="#">Features</flux:navbar.item>    <flux:navbar.item href="#">Pricing</flux:navbar.item>    <flux:navbar.item href="#">About</flux:navbar.item></flux:navbar>
```

## [Detecting the current page](https://fluxui.dev/components/navbar#detecting-the-current-page)

Navbars and navlists will try to automatically detect and mark the current page based on the href attribute passed in. However, if you need full control, you can pass the current prop to the item directly.

```
<flux:navbar.item href="/" current>Home</flux:navbar.item><flux:navbar.item href="/" :current="false">Home</flux:navbar.item><flux:navbar.item href="/" :current="request()->is('/')">Home</flux:navbar.item>
```

## [With icons](https://fluxui.dev/components/navbar#with-icons)

Add a leading icons for visual context.

```
<flux:navbar>    <flux:navbar.item href="#" icon="home">Home</flux:navbar.item>    <flux:navbar.item href="#" icon="puzzle-piece">Features</flux:navbar.item>    <flux:navbar.item href="#" icon="currency-dollar">Pricing</flux:navbar.item>    <flux:navbar.item href="#" icon="user">About</flux:navbar.item></flux:navbar>
```

## [With badges](https://fluxui.dev/components/navbar#with-badges)

Add a trailing badge to a navbar item using the badge prop.

```
<flux:navbar>    <flux:navbar.item href="#">Home</flux:navbar.item>    <flux:navbar.item href="#" badge="12">Inbox</flux:navbar.item>    <flux:navbar.item href="#">Contacts</flux:navbar.item>    <flux:navbar.item href="#" badge="Pro" badge-color="lime">Calendar</flux:navbar.item></flux:navbar>
```

## [Dropdown navigation](https://fluxui.dev/components/navbar#dropdown-navigation)

Condense multiple navigation items into a single dropdown menu to save on space and group related items.

```
<flux:navbar>    <flux:navbar.item href="#">Dashboard</flux:navbar.item>    <flux:navbar.item href="#">Transactions</flux:navbar.item>    <flux:dropdown>        <flux:navbar.item icon:trailing="chevron-down">Account</flux:navbar.item>        <flux:navmenu>            <flux:navmenu.item href="#">Profile</flux:navmenu.item>            <flux:navmenu.item href="#">Settings</flux:navmenu.item>            <flux:navmenu.item href="#">Billing</flux:navmenu.item>        </flux:navmenu>    </flux:dropdown></flux:navbar>
```

## [Navlist (sidebar)](https://fluxui.dev/components/navbar#navlist-sidebar)

Arrange your navbar vertically using the navlist component.

```
<flux:navlist class="w-64">    <flux:navlist.item href="#" icon="home">Home</flux:navlist.item>    <flux:navlist.item href="#" icon="puzzle-piece">Features</flux:navlist.item>    <flux:navlist.item href="#" icon="currency-dollar">Pricing</flux:navlist.item>    <flux:navlist.item href="#" icon="user">About</flux:navlist.item></flux:navlist>
```

## [Navlist group](https://fluxui.dev/components/navbar#navlist-group)

Group related navigation items.

```
<flux:navlist>    <flux:navlist.group heading="Account" class="mt-4">        <flux:navlist.item href="#">Profile</flux:navlist.item>        <flux:navlist.item href="#">Settings</flux:navlist.item>        <flux:navlist.item href="#">Billing</flux:navlist.item>    </flux:navlist.group></flux:navlist>
```

## [Collapsible groups](https://fluxui.dev/components/navbar#collapsible-groups)

Group related navigation items into collapsible sections using the expandable prop.

```
<flux:navlist class="w-64">    <flux:navlist.item href="#" icon="home">Dashboard</flux:navlist.item>    <flux:navlist.item href="#" icon="list-bullet">Transactions</flux:navlist.item>    <flux:navlist.group heading="Account" expandable>        <flux:navlist.item href="#">Profile</flux:navlist.item>        <flux:navlist.item href="#">Settings</flux:navlist.item>        <flux:navlist.item href="#">Billing</flux:navlist.item>    </flux:navlist.group></flux:navlist>
```

If you want a group to be collapsed by default, you can use the expanded prop:

```
<flux:navlist.group heading="Account" expandable :expanded="false">
```

## [Navlist badges](https://fluxui.dev/components/navbar#navlist-badges)

Show additional information related to a navlist item using the badge prop.

```
<flux:navlist class="w-64">    <flux:navlist.item href="#" icon="home">Home</flux:navlist.item>    <flux:navlist.item href="#" icon="envelope" badge="12">Inbox</flux:navlist.item>    <flux:navlist.item href="#" icon="user-group">Contacts</flux:navlist.item>    <flux:navlist.item href="#" icon="calendar-days" badge="Pro" badge-color="lime">Calendar</flux:navlist.item></flux:navlist>
```

## ## Reference

### [flux:navbar](https://fluxui.dev/components/navbar#fluxnavbar)

A horizontal navigation container.

|Slot|Description|
|---|---|
|default|The navigation items.|

|Attribute|Description|
|---|---|
|data-flux-navbar|Applied to the root element for styling and identification.|

### [flux:navbar.item](https://fluxui.dev/components/navbar#fluxnavbaritem)

|Prop|Description|
|---|---|
|href|URL the item links to.|
|current|If true, applies active styling to the item. Auto-detected based on current URL if not specified.|
|icon|Name of the icon to display at the start of the item.|
|icon:trailing|Name of the icon to display at the end of the item.|

|Attribute|Description|
|---|---|
|data-current|Applied when the item is active/current.|

### [flux:navlist](https://fluxui.dev/components/navbar#fluxnavlist)

A vertical navigation container (sidebar).

|Slot|Description|
|---|---|
|default|The navigation items and groups.|

|Attribute|Description|
|---|---|
|data-flux-navlist|Applied to the root element for styling and identification.|

### [flux:navlist.item](https://fluxui.dev/components/navbar#fluxnavlistitem)

|Prop|Description|
|---|---|
|href|URL the item links to.|
|current|If true, applies active styling to the item. Auto-detected based on current URL if not specified.|
|icon|Name of the icon to display at the start of the item.|

|Attribute|Description|
|---|---|
|data-current|Applied when the item is active/current.|

### [flux:navlist.group](https://fluxui.dev/components/navbar#fluxnavlistgroup)

|Prop|Description|
|---|---|
|heading|Text displayed as the group heading.|
|expandable|If true, makes the group collapsible.|
|expanded|If true, expands the group by default when expandable.|

|Slot|Description|
|---|---|
|default|The group's navigation items.|

### [Profile switcher](https://fluxui.dev/components/navbar#profile-switcher)

