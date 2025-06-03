Collapse and expand sections of content. Perfect for FAQs and content-heavy areas.

<flux:accordion>
    <flux:accordion.item>
        <flux:accordion.heading>What's your refund policy?</flux:accordion.heading>

        <flux:accordion.content>
            If you are not satisfied with your purchase, we offer a 30-day money-back guarantee. Please contact our support team for assistance.
        </flux:accordion.content>
    </flux:accordion.item>

    <flux:accordion.item>
        <flux:accordion.heading>Do you offer any discounts for bulk purchases?</flux:accordion.heading>

        <flux:accordion.content>
            Yes, we offer special discounts for bulk orders. Please reach out to our sales team with your requirements.
        </flux:accordion.content>
    </flux:accordion.item>

    <flux:accordion.item>
        <flux:accordion.heading>How do I track my order?</flux:accordion.heading>

        <flux:accordion.content>
            Once your order is shipped, you will receive an email with a tracking number. Use this number to track your order on our website.
        </flux:accordion.content>
    </flux:accordion.item>
</flux:accordion>

## [Shorthand](https://fluxui.dev/components/accordion#shorthand)

You can save on markup by passing the heading text as a prop directly.

<flux:accordion.item heading="What's your refund policy?">
    If you are not satisfied with your purchase, we offer a 30-day money-back guarantee. Please contact our support team for assistance.
</flux:accordion.item>

## [Disabled](https://fluxui.dev/components/accordion#disabled)

Restrict an accordion item from being expanded.

<flux:accordion.item disabled>
    <!-- ... -->
</flux:accordion.item>

## [Exclusive](https://fluxui.dev/components/accordion#exclusive)

Enforce that only a single accordion item is expanded at a time.

<flux:accordion exclusive>
    <!-- ... -->
</flux:accordion>

## [Expanded](https://fluxui.dev/components/accordion#expanded)

Expand a specific accordion by default.

<flux:accordion.item expanded>
    <!-- ... -->
</flux:accordion.item>

## [Leading icon](https://fluxui.dev/components/accordion#leading-icon)

Display the icon before the heading instead of after it.

<flux:accordion variant="reverse">
    <!-- ... -->
</flux:accordion>

## Reference

### [flux:accordion](https://fluxui.dev/components/accordion#fluxaccordion)

|Prop|Description|
|---|---|
|variant|When set to reverse, displays the icon before the heading instead of after it.|
|transition|If true, enables expanding transitions for smoother interactions. Default: false.|
|exclusive|If true, only one accordion item can be expanded at a time. Default: false.|

### [flux:accordion.item](https://fluxui.dev/components/accordion#fluxaccordionitem)

|Prop|Description|
|---|---|
|heading|Shorthand for flux:accordion.heading content.|
|expanded|If true, the accordion item is expanded by default. Default: false.|
|disabled|If true, the accordion item cannot be expanded or collapsed. Default: false.|

### [flux:accordion.heading](https://fluxui.dev/components/accordion#fluxaccordionheading)

|Slot|Description|
|---|---|
|default|The heading text.|

### [flux:accordion.content](https://fluxui.dev/components/accordion#fluxaccordioncontent)

|Slot|Description|
|---|---|
|default|The content to display when the accordion item is expanded.|

