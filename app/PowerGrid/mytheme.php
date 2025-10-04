<?php

namespace App\PowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class MyTheme extends Tailwind
{
    public string $name = 'tailwind';

    public function table(): array
    {
        return [
            'layout' => [
                // wrapper
                'base'      => 'w-full p-2 sm:p-3 lg:p-4',
                // tarjeta: en claro blanco; en oscuro TRANSPARENTE para que se funda con el fondo
                'div'       => 'relative rounded-xl border border-gray-200 bg-white dark:border-neutral-700/40 dark:bg-transparent',
                // tipografía
                'table'     => 'w-full table-auto text-sm text-gray-700 dark:text-neutral-200',
                // sin scroll horizontal
                'container' => 'w-full',
                // acciones
                'actions'   => 'flex gap-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium',
            ],

           'header' => [
    // En claro: gris suave | En oscuro: gris neutro más oscuro (diferente del body)
    'thead'    => 'bg-gray-50 dark:bg-neutral-900/80',
    'tr'       => '',
    'th'       => 'px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-neutral-200',
    'thAction' => '!font-semibold',
],

            'body' => [
                'tbody'           => 'text-gray-700 dark:text-neutral-200',
                'tbodyEmpty'      => 'text-gray-500 dark:text-neutral-400',
                // divisores y hover en NEUTRO (sin azules)
                'tr'              => 'border-t border-gray-200 hover:bg-gray-50 dark:border-neutral-700/40 dark:hover:bg-neutral-800/40',
                'td'              => 'px-4 py-4 align-middle text-gray-700 dark:text-neutral-200',
                'tdEmpty'         => 'px-4 py-4 text-gray-500 dark:text-neutral-400',
                'tdSummarize'     => 'px-4 py-3 text-right text-gray-500 dark:text-neutral-400 text-sm',
                'trSummarize'     => 'border-t border-gray-200 dark:border-neutral-700/40',
                'tdFilters'       => 'px-4 py-2',
                // fila de filtros: también transparente en oscuro
                'trFilters'       => 'bg-gray-50 dark:bg-transparent',
                'tdActionsContainer' => 'flex gap-3 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium',
            ],
        ];
    }

    public function footer(): array
    {
        return [
            'view' => $this->root().'.footer',
            'select' => 'appearance-none bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 rounded-md px-3 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
            // footer transparente en oscuro para que se integre al fondo
            'footer' => 'rounded-b-xl border-t border-gray-200 bg-white dark:border-neutral-700/40 dark:bg-transparent',
            'footer_with_pagination' => 'flex flex-col md:flex-row items-center justify-between gap-3 px-3 py-3',
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
            'input' => 'w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 placeholder:text-gray-400 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
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
            'th' => 'px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-neutral-300/90',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'checkbox checkbox-default checkbox-sm' ,
        ];
    }

    public function radio(): array
    {
        return [
            'th' => 'px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-neutral-300/90',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'h-4 w-4 rounded-full text-blue-600 focus:ring-blue-600 dark:text-blue-500 dark:focus:ring-blue-500',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view' => $this->root().'.filters.boolean',
            'base' => 'min-w-[5rem]',
            'select' => 'w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base' => '',
            'view' => $this->root().'.filters.date-picker',
            'input' => 'flatpickr flatpickr-input w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
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
            'input' => 'w-full min-w-[5rem] rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view' => $this->root().'.filters.select',
            'base' => '',
            'select' => 'w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view' => $this->root().'.filters.input-text',
            'base' => 'min-w-[9.5rem]',
            'select' => 'w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
            'input'  => 'w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input' => 'pl-8 w-full rounded-md bg-white text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-600 px-2 py-1.5 placeholder:text-gray-400 dark:bg-neutral-800 dark:text-neutral-200 dark:ring-neutral-700/40 dark:focus:ring-blue-500',
            'iconClose'  => 'text-gray-400 dark:text-neutral-400',
            'iconSearch' => 'text-gray-400 dark:text-neutral-400 mr-2 w-5 h-5',
        ];
    }
}