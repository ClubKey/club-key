import type { VariantProps } from 'class-variance-authority';
import { cva } from 'class-variance-authority';

export { default as Badge } from './Badge.vue';

export const badgeVariants = cva(
    'inline-flex items-center justify-center rounded-md border text-xs font-medium w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden',
    {
        variants: {
            variant: {
                default: 'border-transparent bg-primary text-primary-foreground hover:bg-primary/90',
                secondary: 'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/90',
                destructive:
                    'border-transparent bg-destructive text-white hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60',
                outline: 'text-foreground hover:bg-accent hover:text-accent-foreground'
            },
            size: {
                sm: 'px-2 py-0.5 text-xs',
                md: 'px-3 py-1 text-sm',
                lg: 'px-4 py-1.5 text-base'
            }
        },
        defaultVariants: {
            variant: 'default',
            size: 'md'
        }
    }
);

export type BadgeVariants = VariantProps<typeof badgeVariants>
