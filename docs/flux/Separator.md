Visually divide sections of content or groups of items.

```
<flux:separator />
```

## [With text](https://fluxui.dev/components/separator#with-text)

Add text to the separator for a more descriptive element.

```
<flux:separator text="or" />
```

## [Vertical](https://fluxui.dev/components/separator#vertical)

Seperate contents with a vertical seperator when horizontally stacked.

```
<flux:separator vertical />
```

## [Limited height](https://fluxui.dev/components/separator#limited-height)

You can limit the height of the vertical separator by adding vertical margin.

```
<flux:separator vertical class="my-2" />
```

## [Subtle](https://fluxui.dev/components/separator#subtle)

Flux offers a subtle variant for a separator that blends into the background.

```
<flux:separator vertical variant="subtle" />
```

## Reference

### [flux:separator](https://fluxui.dev/components/separator#fluxseparator)

|Prop|Description|
|---|---|
|vertical|Displays a vertical separator. Default is horizontal.|
|variant|Visual style variant. Options: subtle. Default: standard separator.|
|text|Optional text to display in the center of the separator.|
|orientation|Alternative to vertical prop. Options: horizontal, vertical. Default: horizontal.|

|Class|Description|
|---|---|
|my-*|Commonly used to shorten vertical separators.|

|Attribute|Description|
|---|---|
|data-flux-separator|Applied to the root element for styling and identification.|