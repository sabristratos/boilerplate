Highlight information like status, category, or count.

<flux:badge color="lime">New</flux:badge>

## [Sizes](https://fluxui.dev/components/badge#sizes)

Choose between three different sizes for your badges with the size prop.

<flux:badge size="sm">Small</flux:badge>
<flux:badge>Default</flux:badge>
<flux:badge size="lg">Large</flux:badge>

## [Icons](https://fluxui.dev/components/badge#icons)

Add icons to badges with the icon and icon:trailing props.

<flux:badge icon="user-circle">Users</flux:badge>
<flux:badge icon="document-text">Files</flux:badge>
<flux:badge icon:trailing="video-camera">Videos</flux:badge>

## [Pill variant](https://fluxui.dev/components/badge#pill-variant)

Display badges with a fully rounded border radius using the variant="pill" prop.

<flux:badge variant="pill" icon="user">Users</flux:badge>

## [As button](https://fluxui.dev/components/badge#as-button)

Make the entire badge clickable by wrapping it in a button element.

<flux:badge as="button" variant="pill" icon="plus" size="lg">Amount</flux:badge>

## [With close button](https://fluxui.dev/components/badge#with-close-button)

Make a badge removable by appending a close button.

<flux:badge>
    Admin <flux:badge.close />
</flux:badge>

## [Colors](https://fluxui.dev/components/badge#colors)

Choose from an array of colors to differentiate between badges and convey emotion.

<flux:badge color="zinc">Zinc</flux:badge>
<flux:badge color="red">Red</flux:badge>
<flux:badge color="orange">Orange</flux:badge>
<flux:badge color="amber">Amber</flux:badge>
<flux:badge color="yellow">Yellow</flux:badge>
<flux:badge color="lime">Lime</flux:badge>
<flux:badge color="green">Green</flux:badge>
<flux:badge color="emerald">Emerald</flux:badge>
<flux:badge color="teal">Teal</flux:badge>
<flux:badge color="cyan">Cyan</flux:badge>
<flux:badge color="sky">Sky</flux:badge>
<flux:badge color="blue">Blue</flux:badge>
<flux:badge color="indigo">Indigo</flux:badge>
<flux:badge color="violet">Violet</flux:badge>
<flux:badge color="purple">Purple</flux:badge>
<flux:badge color="fuchsia">Fuchsia</flux:badge>
<flux:badge color="pink">Pink</flux:badge>
<flux:badge color="rose">Rose</flux:badge>

## [Solid variant](https://fluxui.dev/components/badge#solid-variant)

Bold, high-contrast badges for more important status indicators or alerts.

<flux:badge variant="solid" color="zinc">Zinc</flux:badge>
<flux:badge variant="solid" color="red">Red</flux:badge>
<flux:badge variant="solid" color="orange">Orange</flux:badge>
<flux:badge variant="solid" color="amber">Amber</flux:badge>
<flux:badge variant="solid" color="yellow">Yellow</flux:badge>
<flux:badge variant="solid" color="lime">Lime</flux:badge>
<flux:badge variant="solid" color="green">Green</flux:badge>
<flux:badge variant="solid" color="emerald">Emerald</flux:badge>
<flux:badge variant="solid" color="teal">Teal</flux:badge>
<flux:badge variant="solid" color="cyan">Cyan</flux:badge>
<flux:badge variant="solid" color="sky">Sky</flux:badge>
<flux:badge variant="solid" color="blue">Blue</flux:badge>
<flux:badge variant="solid" color="indigo">Indigo</flux:badge>
<flux:badge variant="solid" color="violet">Violet</flux:badge>
<flux:badge variant="solid" color="purple">Purple</flux:badge>
<flux:badge variant="solid" color="fuchsia">Fuchsia</flux:badge>
<flux:badge variant="solid" color="pink">Pink</flux:badge>
<flux:badge variant="solid" color="rose">Rose</flux:badge>

## [Inset](https://fluxui.dev/components/badge#inset)

If you're using badges alongside inline text, you might run into spacing issues because of the extra padding around the badge. Use the inset prop to add negative margins to the top and bottom of the badge to avoid this.

<flux:heading>
    Page builder <flux:badge color="lime" inset="top bottom">New</flux:badge>
</flux:heading>

<flux:text class="mt-2">Easily author new pages without leaving your browser.</flux:text>

## Reference

### [flux:badge](https://fluxui.dev/components/badge#fluxbadge)

|Prop|Description|
|---|---|
|color|Badge color (e.g., zinc, red, blue). Default: zinc.|
|size|Badge size. Options: sm, lg.|
|variant|Badge style variant. Options: pill.|
|icon|Name of the icon to display before the badge text.|
|icon:trailing|Name of the icon to display after the badge text.|
|icon:variant|Icon variant. Options: outline, solid, mini, micro. Default: mini.|
|as|HTML element to render the badge as. Options: button. Default: div.|
|inset|Add negative margins to specific sides. Options: top, bottom, left, right, or any combination of the four.|

### [flux:badge.close](https://fluxui.dev/components/badge#fluxbadgeclose)

|Prop|Description|
|---|---|
|icon|Name of the icon to display. Default: x-mark.|
|icon:variant|Icon variant. Options: outline, solid, mini, micro. Default: mini.|

