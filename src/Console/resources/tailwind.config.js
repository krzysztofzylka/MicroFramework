/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.js",
    './src/**/*.twig',
    './template/**/*.twig',
    './resources/**/*.twig',
    "./resources/**/*.js"
  ],
  theme: {
    extend: {
    },
  },
  plugins: [
  ],
  safelist: [
    //table, dialogbox
    'absolute', 'bg-white', 'rounded-lg', 'shadow', 'dark:bg-gray-700', 'flex', 'items-center', 'justify-between', 'p-4', 'md:p-5', 'border-b',
    'rounded-t', 'dark:border-gray-600', 'text-xl', 'font-semibold', 'text-gray-900', 'dark:text-white', 'text-gray-400', 'bg-transparent',
    'hover:bg-gray-200', 'hover:text-gray-900', 'text-sm', 'w-8', 'h-8', 'ms-auto', 'inline-flex', 'justify-center', 'dark:hover:bg-gray-600',
    'dark:hover:text-white', 'space-y-4', 'flex-column', 'flex-wrap', 'md:flex-row', 'md:space-y-0', 'dark:bg-gray-900', 'font-medium', 'text-blue-600',
    'dark:text-blue-500', 'hover:underline', 'text-xs', 'me-2', 'px-2.5', 'py-0.5', 'rounded', 'rounded-full', 'text-left', 'font-normal',
    'text-gray-500', 'dark:text-gray-400', 'mb-4', 'md:mb-0', 'block', 'w-full', 'md:inline', 'md:w-auto', '-space-x-px', 'rtl:space-x-reverse',
    'px-3', 'ms-0', 'leading-tight', 'border', 'border-gray-300', 'hover:bg-gray-100', 'hover:text-gray-700', 'dark:bg-gray-800', 'dark:border-gray-700',
    'dark:hover:bg-gray-700', 'opacity-50', 'cursor-not-allowed', 'bg-blue-50', 'hover:bg-blue-100', 'hover:text-blue-700', 'text-white', 'bg-blue-700',
    'hover:bg-blue-800', 'focus:ring-4', 'focus:ring-blue-300', 'px-5', 'py-2.5', 'mb-2', 'dark:bg-blue-600', 'dark:hover:bg-blue-700', 'focus:outline-none',
    'dark:focus:ring-blue-800', 'inset-y-0', 'rtl:inset-r-0', 'start-0', 'ps-3', 'pointer-events-none', 'p-2', 'ps-10', 'w-80', 'bg-gray-50',
    'focus:ring-blue-500', 'focus:border-blue-500', 'dark:placeholder-gray-400', 'dark:focus:ring-blue-500', 'dark:focus:border-blue-500', 'p-0', 'm-0',
    'relative', 'rtl:text-right', 'px-6', 'py-2', 'py-3', 'py-4', 'text-gray-700', 'uppercase', 'hover:bg-gray-50', 'md:w-1/2', 'w-4', 'h-4', 'rounded-s-lg',
    'rounded-e-lg', 'bg-red-50', 'border-red-500', 'text-red-900', 'placeholder-red-700', 'focus:ring-red-500', 'focus:border-red-500', 'p-2.5',
    'dark:bg-red-100', 'dark:border-red-400', 'sm:text-sm', 'focus:ring-primary-600', 'focus:border-primary-600', 'cursor-pointer', 'border-red-300',
    'dark:text-red-300', 'dark:bg-red-900', 'dark:border-red-600', 'dark:placeholder-red-400', 'px-0', 'border-0', 'focus:ring-0', 'line-flex', 'px-4',
    'text-center', 'focus:ring-blue-200', 'dark:focus:ring-blue-900', 'focus:ring-red-100', 'bg-gray-100', 'dark:focus:ring-blue-600', 'dark:ring-offset-gray-800',
    'dark:focus:ring-offset-gray-800', 'focus:ring-2', 'mr-2', 'w-11', 'h-6', 'bg-gray-200', 'peer-focus:outline-none', 'peer-focus:ring-4',
    'peer-focus:ring-blue-300', 'dark:peer-focus:ring-blue-800', 'peer', 'peer-checked:after:translate-x-full', 'rtl:peer-checked:after:-translate-x-full',
    'peer-checked:after:border-white', 'after:content-[\'\']', 'after:absolute', 'after:top-[2px]', 'after:start-[2px]', 'after:bg-white', 'after:border-gray-300',
    'after:border', 'after:rounded-full', 'after:h-5', 'after:w-5', 'after:transition-all', 'peer-checked:bg-blue-600', 'mt-2', 'sm:w-auto', 'pl-10', 'left-0',
    'pl-3.5', 'mt-1', 'dark:text-gray-300', 'rounded-t-lg', 'dark:bg-red-800', 'border-t', 'ml-2', 'h-5'
  ]
}

