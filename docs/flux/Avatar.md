Display an image or initials as an avatar.

<flux:avatar src="https://unavatar.io/x/calebporzio" />

## [Tooltip](https://fluxui.dev/components/avatar#tooltip)

Use the tooltip prop to display a tooltip when hovering over the avatar.

<flux:avatar tooltip="Caleb Porzio" src="https://unavatar.io/x/calebporzio" />

<!-- Or infer from the name prop... -->
<flux:avatar tooltip name="Caleb Porzio" src="https://unavatar.io/x/calebporzio" />

## [Initials](https://fluxui.dev/components/avatar#initials)

When no src is provided, the name prop will be used to automatically generate initials. You can also use the initials prop directly.

<flux:avatar name="Caleb Porzio" />
<flux:avatar name="calebporzio" />
<flux:avatar name="calebporzio" initials:single />

<!-- Or use the initials prop directly... -->
<flux:avatar initials="CP" />

## [Size](https://fluxui.dev/components/avatar#size)

Use the size prop to change the size of the avatar.

<!-- Extra large: size-16 (64px) -->
<flux:avatar size="xl" src="https://unavatar.io/x/calebporzio" />

<!-- Large: size-12 (48px) -->
<flux:avatar size="lg" src="https://unavatar.io/x/calebporzio" />

<!-- Default: size-10 (40px) -->
<flux:avatar src="https://unavatar.io/x/calebporzio" />

<!-- Small: size-8 (32px) -->
<flux:avatar size="sm" src="https://unavatar.io/x/calebporzio" />

<!-- Extra small: size-6 (24px) -->
<flux:avatar size="xs" src="https://unavatar.io/x/calebporzio" />

## [Icon](https://fluxui.dev/components/avatar#icon)

Use the icon prop to display an icon instead of an image.

<flux:avatar icon="user" />
<flux:avatar icon="phone" />
<flux:avatar icon="computer-desktop" />

## [Colors](https://fluxui.dev/components/avatar#colors)

Use the color prop to change the color of the avatar.

<flux:avatar name="Caleb Porzio" color="red" />
<flux:avatar name="Caleb Porzio" color="orange" />
<flux:avatar name="Caleb Porzio" color="amber" />
<flux:avatar name="Caleb Porzio" color="yellow" />
<flux:avatar name="Caleb Porzio" color="lime" />
<flux:avatar name="Caleb Porzio" color="green" />
<flux:avatar name="Caleb Porzio" color="emerald" />
<flux:avatar name="Caleb Porzio" color="teal" />
<flux:avatar name="Caleb Porzio" color="cyan" />
<flux:avatar name="Caleb Porzio" color="sky" />
<flux:avatar name="Caleb Porzio" color="blue" />
<flux:avatar name="Caleb Porzio" color="indigo" />
<flux:avatar name="Caleb Porzio" color="violet" />
<flux:avatar name="Caleb Porzio" color="purple" />
<flux:avatar name="Caleb Porzio" color="fuchsia" />
<flux:avatar name="Caleb Porzio" color="pink" />
<flux:avatar name="Caleb Porzio" color="rose" />

## [Auto color](https://fluxui.dev/components/avatar#auto-color)

Deterministically generate a color based on a user's name.

<flux:avatar name="Caleb Porzio" color="auto" />

<!-- Use color:seed to generate a consistent color based -->
<!-- on something unchanging like a user's ID... -->
<flux:avatar name="Caleb Porzio" color="auto" color:seed="{{ $user->id }}" />

## [Circle](https://fluxui.dev/components/avatar#circle)

Use the circle prop to make the avatar circular.

<flux:avatar circle src="https://unavatar.io/x/calebporzio" />

## [Badge](https://fluxui.dev/components/avatar#badge)

Add badges to avatars in various ways. Use the badge prop by itself for a simple dot indicator, provide content like numbers or emojis, or even pass in custom HTML via a slot.

<flux:avatar badge badge:color="green" src="https://unavatar.io/x/calebporzio" />

<flux:avatar badge badge:color="zinc" badge:position="top right" badge:circle badge:variant="outline" src="https://unavatar.io/x/calebporzio" />

<flux:avatar badge="25" src="https://unavatar.io/x/calebporzio" />

<flux:avatar circle badge="ð" badge:circle src="https://unavatar.io/x/calebporzio" />

<flux:avatar circle src="https://unavatar.io/x/calebporzio">
    <x-slot:badge>
        <img class="size-3" src="https://unavatar.io/github/hugosaintemarie" />
    </x-slot:badge>
</flux:avatar>


## [Groups](https://fluxui.dev/components/avatar#groups)

Stack avatars together. By default, grouped avatars have rings that adapt to your theme - white in light mode and a dark color in dark mode. If you need to customize the ring color, to match a different background, you can do so by adding a custom class to the flux:avatar.group component.

<flux:avatar.group>
    <flux:avatar src="https://unavatar.io/x/calebporzio" />
    <flux:avatar src="https://unavatar.io/github/hugosaintemarie" />
    <flux:avatar src="https://unavatar.io/github/joshhanley" />
    <flux:avatar>3+</flux:avatar>
</flux:avatar.group>

<!-- Adapt rings to custom background... -->
<flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
    <flux:avatar circle src="https://unavatar.io/x/calebporzio" />
    <flux:avatar circle src="https://unavatar.io/github/hugosaintemarie" />
    <flux:avatar circle src="https://unavatar.io/github/joshhanley" />
    <flux:avatar circle>3+</flux:avatar>
</flux:avatar.group>

## [As button](https://fluxui.dev/components/avatar#as-button)

Use the as prop to make the avatar a button.

<flux:avatar as="button" src="https://unavatar.io/x/calebporzio" />

## [As link](https://fluxui.dev/components/avatar#as-link)

Use the href prop to make the avatar a link.

<flux:avatar href="https://x.com/calebporzio" src="https://unavatar.io/x/calebporzio" />

## Examples

### [Testimonial](https://fluxui.dev/components/avatar#testimonial)


<div>
    <div class="flex items-center gap-2">
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
        <flux:icon.star variant="solid" />
    </div>

    <flux:heading size="xl" class="mt-4 italic">
        <p>IMO Livewire takes Blade to the next level. It's basically what Blade should be by default. ð¥</p>
    </flux:heading>

    <div class="mt-6 flex items-center gap-4">
        <flux:avatar size="lg" src="https://unavatar.io/x/taylorotwell" />

        <div>
            <flux:heading size="lg">Taylor Otwell</flux:heading>
            <flux:text>Creator of Laravel</flux:text>
        </div>
    </div>
</div>

### [Members table](https://fluxui.dev/components/avatar#members-table)

Include avatars in table rows to make user data more identifiable.

<div class="flex justify-between items-center mb-4">
    <flux:heading size="lg">Team members</flux:heading>

    <flux:button size="sm" icon="plus">Invite</flux:button>
</div>

<flux:table>
    <flux:table.rows>
        <flux:table.row>
            <flux:table.cell>
                <div class="flex items-center gap-2 sm:gap-4">
                    <flux:avatar circle size="lg" class="max-sm:size-8" src="https://unavatar.io/github/calebporzio" />
                    <div class="flex flex-col">
                        <flux:heading>Caleb Porzio <flux:badge size="sm" color="blue" class="ml-1 max-sm:hidden">You</flux:badge></flux:heading>
                        <flux:text class="max-sm:hidden">caleb@laravel-livewire.com</flux:text>
                    </div>
                </div>
            </flux:table.cell>

            <flux:table.cell>
                <div class="flex justify-end items-center gap-2">
                    <flux:select size="sm" class="min-w-fit max-w-fit">
                        <flux:select.option value="admin" selected>Admin</flux:select.option>
                        <flux:select.option value="member">Member</flux:select.option>
                        <flux:select.option value="guest">Guest</flux:select.option>
                    </flux:select>
                    <flux:button size="sm" variant="subtle" icon="trash" class="shrink-0" />
                </div>
            </flux:table.cell>
        </flux:table.row>

        <flux:table.row >
            <flux:table.cell>
                <div class="flex items-center gap-2 sm:gap-4">
                    <flux:avatar circle size="lg" class="max-sm:size-8" src="https://unavatar.io/github/hugosaintemarie" />
                    <div class="flex flex-col">
                        <flux:heading>Hugo Sainte-Marie</flux:heading>
                        <flux:text class="max-sm:hidden">hugo@example.com</flux:text>
                    </div>
                </div>
            </flux:table.cell>

            <flux:table.cell>
                <div class="flex justify-end items-center gap-2">
                    <flux:select size="sm" class="min-w-fit max-w-fit">
                        <flux:select.option value="admin">Admin</flux:select.option>
                        <flux:select.option value="member" selected>Member</flux:select.option>
                        <flux:select.option value="guest">Guest</flux:select.option>
                    </flux:select>
                    <flux:button size="sm" variant="subtle" icon="trash" class="shrink-0" />
                </div>
            </flux:table.cell>
        </flux:table.row>

        <flux:table.row>
            <flux:table.cell>
                <div class="flex items-center gap-2 sm:gap-4">
                    <flux:avatar circle size="lg" class="max-sm:size-8" src="https://unavatar.io/github/joshhanley" />
                    <div class="flex flex-col">
                        <flux:heading>Josh Hanley</flux:heading>
                        <flux:text class="max-sm:hidden">josh@example.com</flux:text>
                    </div>
                </div>
            </flux:table.cell>

            <flux:table.cell>
                <div class="flex justify-end items-center gap-2">
                    <flux:select size="sm" class="min-w-fit max-w-fit">
                        <flux:select.option value="admin">Admin</flux:select.option>
                        <flux:select.option value="member" selected>Member</flux:select.option>
                        <flux:select.option value="guest">Guest</flux:select.option>
                    </flux:select>
                    <flux:button size="sm" variant="subtle" icon="trash" class="shrink-0" />
                </div>
            </flux:table.cell>
        </flux:table.row>
    </flux:table.rows>
</flux:table>


### [Assignees list](https://fluxui.dev/components/avatar#assignees-list)

Include avatars in table rows to make user data more identifiable.

<flux:card>
    <div class="flex justify-between items-center">
        <flux:heading>Assignees</flux:heading>
        <flux:button size="sm" variant="subtle" icon="plus" inset="top bottom" />
    </div>

    <flux:separator class="mt-2 mb-4" variant="subtle" />

    <ul class="flex flex-col gap-3">
        <li class="flex items-center gap-2">
            <flux:avatar size="xs" src="https://unavatar.io/github/calebporzio" />
            <flux:heading>Caleb Porzio</flux:heading>
        </li>
        <li class="flex items-center gap-2">
            <flux:avatar size="xs" src="https://unavatar.io/github/hugosaintemarie" />
            <flux:heading>Hugo Sainte-Marie</flux:heading>
        </li>
        <li class="flex items-center gap-2">
            <flux:avatar size="xs" src="https://unavatar.io/github/joshhanley" />
            <flux:heading>Josh Hanley</flux:heading>
        </li>
        <li class="flex items-center gap-2">
            <flux:avatar size="xs" src="https://unavatar.io/github/jasonlbeggs" />
            <flux:heading>Jason Beggs</flux:heading>
        </li>
    </ul>
</flux:card>

### [User popover](https://fluxui.dev/components/avatar#user-popover)

Display a user's profile information in a popover.

<flux:dropdown hover position="bottom center">
    <flux:avatar as="button" name="calebporzio" src="https://unavatar.io/x/calebporzio" />

    <flux:popover class="relative max-w-[15rem]">
        <div class="absolute top-0 right-0 p-2">
            <flux:button icon="user-plus" variant="filled" size="sm">Follow back</flux:button>
        </div>

        <flux:avatar size="xl" name="calebporzio" src="https://unavatar.io/x/calebporzio" />

        <flux:heading class="mt-2">Caleb Porzio</flux:heading>
        <flux:text>@calebporzio <flux:badge color="zinc" size="sm" inset="top bottom">Follows you</flux:badge></flux:text>

        <flux:text class="mt-3">
            I'm a full stack developer with a passion for building web applications. Currently working on a new project called <flux:link href="https://fluxui.dev">Flux</flux:link>.
        </flux:text>

        <div class="flex gap-4 mt-3">
            <div class="flex gap-2 items-center">
                <flux:heading>1.2k</flux:heading> <flux:text>Followers</flux:text>
            </div>

            <div class="flex gap-2 items-center">
                <flux:heading>1.2k</flux:heading> <flux:text>Following</flux:text>
            </div>
        </div>
    </flux:popover>
</flux:dropdown>
