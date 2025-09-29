<?php

namespace App\PowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class FluxLike extends Tailwind
{
    public string $name = 'tailwind';

    public function table(): array
{
     return [
            // layout
'layout' => [
    'base' => 'block align-middle w-full sm:px-6 lg:px-8',
    'div' => 'rounded-t-lg relative border-x border-t border-base-300 bg-base-100', // ← token DaisyUI
    'table' => 'table table-zebra w-full
                [&_th]:text-base-content [&_td]:text-base-content
                dark:[&_tr:nth-child(even)]:bg-base-200
                dark:[&_tr:hover]:bg-base-300
                [&_tr:hover]:bg-base-200',
    // nada de -mx para evitar desbordes/blancos raros
    'container' => '-my-2 overflow-x-auto',
    'actions' => 'gap-2',
],

        'header' => [
                'thead' => 'text-base-content !capitalize',
                'tr' => 'bg-base-200',
                'th' => '',
                'thAction' => '',
            ],

        'body' => [
            'tbody'              => 'divide-y divide-base-200 text-base-content bg-transparent',
            'tbodyEmpty'         => 'text-center text-base-content/60',
            'tr'                 => 'hover:bg-base-50 hover:bg-base-100 transition-colors duration-200',
            'td'                 => 'px-4 py-3 whitespace-nowrap',
            'tdEmpty'            => 'p-3 text-base-content/60',
            'tdSummarize'        => 'p-3 text-sm text-base-content/60 text-right space-y-2',
            'trSummarize'        => '',
            'tdFilters'          => '',
            'trFilters'          => '',
            'tdActionsContainer' => 'flex gap-2 justify-end',
        ],

        // footer
'footer' => [
    'view' => $this->root().'.footer',
    'select' => 'select flex rounded-md py-1.5 px-4 pr-7 w-auto',
    'footer' => 'border-x border-b rounded-b-lg border-b !border-base-200 !text-base-content bg-base-100',
    'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 pl-6 pr-6 relative !text-base-content bg-base-100',
],
    ];
}

    public function footer(): array
    {
         return [
        'view'   => $this->root() . '.footer',
        'select' => 'appearance-none rounded-md ring-1 ring-base-300 focus:ring-2 focus:ring-primary/40 bg-base-100 text-base-content py-1.5 px-3 sm:text-sm',
        'td'     => 'px-4 py-2 text-sm text-base-content bg-base-100  ', // 👈 solo borde arriba
        'tr'     => '',
        'div'    => 'flex items-center justify-between px-4 py-2', // 👈 sin bordes laterales
    ];
    }

    public function searchBox(): array
    {
        return [
           'input' => 'w-full rounded-md ring-1 ring-base-300 focus:ring-2 focus:ring-primary/40 bg-base-100 text-base-content py-2.5 pl-10 sm:text-sm',
            'iconClose'  => 'text-base-content/40',
            'iconSearch' => 'text-base-content/40 mr-2 w-5 h-5',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => 'px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-base-content/60',
            'base'  => 'pt-1',
            'label' => 'flex items-center space-x-3',
            'input' => 'checkbox checkbox-primary checkbox-sm',
        ];
    }
}
