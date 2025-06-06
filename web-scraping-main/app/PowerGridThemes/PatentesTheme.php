<?php

namespace App\PowerGridThemes;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class PatentesTheme extends Tailwind
{
    public string $name = 'tailwind';

    public function table(): array
    {
        return [
            'layout' => [
                'base' => 'p-3 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8',
                'div' => 'rounded-t-lg relative border-x border-t border-white-primary-100 bg-white-primary-700 border-white-primary-600',
                'table' => 'powertable rounded-lg min-w-full border border-white-200 bg-white-600 border-white-5000',
                'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                'actions' => 'flex gap-2',
            ],

            'header' => [
                'thead' => 'shadow-sm bg-gray-100 bg-gray-500 border border-gray-200 border-gray-300',
                'tr' => '',
                'th' => 'font-extrabold px-2 py-2 text-left text-xs text-pg-primary-700 tracking-wider whitespace-nowrap text-pg-primary-300',
                'thAction' => '!font-semibold text-xs',
            ],

            'body' => [
                'tbody' => 'text-pg-primary-800 text-sm',
                'tbodyEmpty' => '',
                'tr' => 'border-b border-pg-primary-100 border-pg-primary-600 hover:bg-pg-primary-50 bg-pg-primary-800 hover:bg-pg-primary-700',
                'td' => 'px-3 py-2 whitespace-nowrap text-pg-primary-200',
                'tdEmpty' => 'p-2 whitespace-nowrap text-pg-primary-200',
                'tdSummarize' => 'p-2 whitespace-nowrap text-pg-primary-200 text-sm text-pg-primary-600 text-right space-y-2',
                'trSummarize' => '',
                'tdFilters' => '',
                'trFilters' => '',
                'tdActionsContainer' => 'flex gap-2',
            ],
        ];
    }

    public function footer(): array
    {
        return [
            'view' => $this->root().'.footer',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-1.5 px-4 pr-7 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
            'footer' => 'border-x border-b rounded-b-lg border-b border-pg-primary-200 bg-pg-primary-700 border-pg-primary-600',
            'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 bg-white overflow-y-auto pl-2 pr-2 relative bg-pg-primary-900',
        ];
    }

    public function cols(): array
    {
        return [
            'div' => 'select-none flex items-center gap-1',
        ];
    }

    public function editable(): array
    {
        return [
            'view' => $this->root().'.editable',
            'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }
    

    public function toggleable(): array
    {
        return [
            'view' => $this->root().'.toggleable',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th' => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-checkbox border-white-600 border-1 bg-white-800 rounded border-white-300 bg-white transition duration-100 ease-in-out h-4 w-4 text-primary-500 focus:ring-primary-500 ring-offset-white-900',
        ];
    }

    public function radio(): array
    {
        return [
            'th' => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-radio rounded-full transition ease-in-out duration-100',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view' => $this->root().'.filters.boolean',
            'base' => 'min-w-[5rem]',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base' => '',
            'view' => $this->root().'.filters.date-picker',
            'input' => 'flatpickr flatpickr-input focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view' => $this->root().'.filters.multi-select',
            'base' => 'inline-block relative w-full',
            'select' => 'mt-1',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view' => $this->root().'.filters.number',
            'input' => 'w-full min-w-[5rem] block focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-1.5 pl-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view' => $this->root().'.filters.select',
            'base' => '',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 border-0 py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-auto min-w-[200px] max-w-full',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view' => $this->root().'.filters.input-text',
            'base' => 'min-w-[9.5rem]',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
            'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 focus-within:ring-primary-600 flex items-center rounded-md ring-1 transition focus-within:ring-2 ring-pg-primary-600 text-pg-primary-300 text-white-600 ring-white-300 bg-pg-primary-800 bg-white placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-white-400 focus:outline-none sm:text-sm sm:leading-6 w-full pl-8',
            'iconClose' => 'text-pg-primary-400 text-pg-primary-200',
            'iconSearch' => 'text-pg-primary-300 mr-2 w-5 h-5 text-pg-primary-200',
        ];
    }
}