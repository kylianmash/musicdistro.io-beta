import { cva, type VariantProps } from 'class-variance-authority';
import clsx from 'clsx';
import type { ButtonHTMLAttributes } from 'react';

const buttonStyles = cva(
  'inline-flex items-center justify-center rounded-md font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none',
  {
    variants: {
      variant: {
        primary: 'bg-emerald-500 text-white hover:bg-emerald-400 focus-visible:ring-emerald-400',
        secondary: 'bg-slate-800 text-slate-100 hover:bg-slate-700 focus-visible:ring-slate-500',
        ghost: 'bg-transparent text-slate-100 hover:bg-slate-800/60 focus-visible:ring-slate-500',
      },
      size: {
        sm: 'h-9 px-3 text-sm',
        md: 'h-10 px-4 text-sm',
        lg: 'h-12 px-6 text-base',
      },
    },
    defaultVariants: {
      variant: 'primary',
      size: 'md',
    },
  }
);

export interface ButtonProps
  extends ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonStyles> {}

export function Button({ className, variant, size, type = 'button', ...props }: ButtonProps) {
  return <button type={type} className={clsx(buttonStyles({ variant, size }), className)} {...props} />;
}
