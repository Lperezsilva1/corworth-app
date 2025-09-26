<?php

namespace App\PowerGridThemes;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class FluxPlain extends Tailwind
{
    // IMPORTANTE: tailwind
    public string $name = 'tailwind';

    public function table(): array
    {
        return [
            'layout' => [
                // sin card, sin bordes externos
                'base'      => 'p-0 w-full align-middle inline-block min-w-full',
                'div'       => 'relative',         // ← sin rounded/border/bg
                'table'     => 'min-w-full w-full table-auto text-sm bg-transparent',
                'container' => '',                 // ← sin scroll wrapper gris
                'actions'   => 'flex gap-2',
            ],
            'header' => [
                'thead'    => 'bg-transparent',    // ← sin fondo
                'tr'       => '',
                'th'       => 'px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-base-content/60 whitespace-nowrap',
                'thAction' => '!font-semibold',
            ],
            'body' => [
                // solo divisores sutiles, hover suave
                'tbody'              => 'divide-y divide-base-200 text-base-content bg-transparent',
                'tbodyEmpty'         => '',
                'tr'                 => 'hover:bg-base-50',
                'td'                 => 'px-4 py-3 whitespace-nowrap',
                'tdEmpty'            => 'p-3 text-base-content/60',
                'tdSummarize'        => 'p-3 text-sm text-base-content/60 text-right space-y-2',
                'trSummarize'        => '',
                'tdFilters'          => '',
                'trFilters'          => '',
                'tdActionsContainer' => 'flex gap-2 justify-end',
            ],
        ];
    }

    public function footer(): array
    {
        return [
            'view'   => $this->root() . '.footer',
            'select' => 'appearance-none !bg-none rounded-md ring-1 ring-base-300 focus:ring-2 focus:ring-primary/40 bg-base-100 text-base-content py-1.5 px-3 sm:text-sm',
        ];
    }

    public function searchBox(): array
    {
        return [
            // input blanco, borde sutil
            'input'      => 'w-full rounded-md ring-1 ring-base-300 focus:ring-2 focus:ring-primary/40 bg-base-100 text-base-content py-1.5 pl-8 sm:text-sm',
            'iconClose'  => 'text-base-content/40',
            'iconSearch' => 'text-base-content/40 mr-2 w-5 h-5',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => 'px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-base-content/60',
            'base'  => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'h-4 w-4 rounded border-base-300 bg-base-100 text-primary focus:ring-primary/50',
        ];
    }
}
