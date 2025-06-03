Help users navigate and understand their place within your application.

<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="#">Blog</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>

## [With slashes](https://fluxui.dev/components/breadcrumbs#with-slashes)

Use slashes instead of chevrons to separate breadcrumb items.

<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" separator="slash">Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="#" separator="slash">Blog</flux:breadcrumbs.item>
    <flux:breadcrumbs.item separator="slash">Post</flux:breadcrumbs.item>
</flux:breadcrumbs>

## [With icon](https://fluxui.dev/components/breadcrumbs#with-icon)

Use an icon instead of text for a particular breadcrumb item.

<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" icon="home" />
    <flux:breadcrumbs.item href="#">Blog</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>

## [With ellipsis](https://fluxui.dev/components/breadcrumbs#with-ellipsis)

Truncate a long breadcrumb list with an ellipsis.

<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" icon="home" />
    <flux:breadcrumbs.item icon="ellipsis-horizontal" />
    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>

## [With ellipsis dropdown](https://fluxui.dev/components/breadcrumbs#with-ellipsis-dropdown)

Truncate a long breadcrumb list into a single ellipsis dropdown.

<flux:breadcrumbs>
    <flux:breadcrumbs.item href="#" icon="home" />

    <flux:breadcrumbs.item>
        <flux:dropdown>
            <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />

            <flux:navmenu>
                <flux:navmenu.item>Client</flux:navmenu.item>
                <flux:navmenu.item icon="arrow-turn-down-right">Team</flux:navmenu.item>
                <flux:navmenu.item icon="arrow-turn-down-right">User</flux:navmenu.item>
            </flux:navmenu>
        </flux:dropdown>
    </flux:breadcrumbs.item>

    <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
</flux:breadcrumbs>

## Reference

### [flux:breadcrumbs](https://fluxui.dev/components/breadcrumbs#fluxbreadcrumbs)

|Slot|Description|
|---|---|
|default|The breadcrumb items to display.|

### [flux:breadcrumbs.item](https://fluxui.dev/components/breadcrumbs#fluxbreadcrumbsitem)

|Prop|Description|
|---|---|
|href|URL the breadcrumb item links to. If omitted, renders as non-clickable text.|
|icon|Name of the icon to display before the badge text.|
|icon:variant|Icon variant. Options: outline, solid, mini, micro. Default: mini.|
|separator|Name of the icon to display as the separator. Default: chevron-right.|

