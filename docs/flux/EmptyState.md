# Empty State

A component for displaying a message when there is no content to show.

```
<flux:empty-state
    icon="document"
    heading="No documents found"
    description="Try a different search term or create a new document."
/>
```

## Basic usage

Use the empty state component to provide feedback when a list, table, or other container has no content to display.

```
<flux:empty-state
    icon="document"
    heading="No documents found"
    description="Try a different search term or create a new document."
/>
```

## With action

You can add buttons or other actions to the empty state by using the default slot.

```
<flux:empty-state
    icon="document"
    heading="No documents found"
    description="Get started by creating your first document."
>
    <flux:button variant="primary">Create document</flux:button>
</flux:empty-state>
```

## Customizing the icon

The icon is displayed in a circular background. You can use any icon from the Heroicons set.

```
<flux:empty-state
    icon="user"
    heading="No users found"
    description="Try a different search term or invite a new user."
/>
```

## Reference

### flux:empty-state

|Prop|Description|
|---|---|
|icon|The name of the icon to display. Uses Heroicons.|
|heading|The heading text to display.|
|description|The description text to display.|

|Slot|Description|
|---|---|
|default|Optional content to display below the description, such as action buttons.|

|Class|Description|
|---|---|
|class|Additional CSS classes to apply to the component.|
