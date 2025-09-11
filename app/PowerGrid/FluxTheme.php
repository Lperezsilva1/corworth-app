<?php

namespace App\PowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

final class FluxTheme extends Tailwind
{
    public function table(): array
    {
        $t = parent::table();

        $t['div']   = ($t['div'] ?? '') . ' rounded-2xl bg-white dark:bg-neutral-900 shadow-md p-4';
        $t['table'] = ($t['table'] ?? '') . ' min-w-full border-separate border-spacing-y-2';

        if (!isset($t['thead'])) $t['thead'] = [];
        $t['thead']['tr'] = ($t['thead']['tr'] ?? '') . ' bg-neutral-100 dark:bg-neutral-800';
        $t['thead']['th'] = ($t['thead']['th'] ?? '') . ' px-4 py-2 text-left text-sm uppercase font-semibold text-gray-600';

        if (!isset($t['tbody'])) $t['tbody'] = [];
        $t['tbody']['tr'] = ($t['tbody']['tr'] ?? '') . ' ';
        $t['tbody']['td'] = ($t['tbody']['td'] ?? '') . ' px-4 py-3 text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-neutral-900 rounded-lg shadow-sm';

        $t['empty'] = ($t['empty'] ?? '') . ' px-4 py-3 text-center text-gray-500 dark:text-gray-400';

        if (!isset($t['search'])) $t['search'] = [];
        $t['search']['input'] = ($t['search']['input'] ?? '') . ' rounded-xl border-neutral-200 dark:border-neutral-700 focus:ring-0';

        return $t;
    }

    public function footer(): array
    {
        $f = parent::footer();

        // ¡No toques $f['view']!
        // Añade clases a una de las claves de contenedor si existen
        foreach (['div','container','wrapper','root','footer'] as $k) {
            if (isset($f[$k])) {
                $f[$k] .= ' flex justify-between items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-300';
                break;
            }
        }

        // Estilos de paginación (si existen en tu versión)
        if (!isset($f['pagination'])) $f['pagination'] = [];
        $f['pagination']['button']  = ($f['pagination']['button'] ?? '') . ' rounded-xl';
        $f['pagination']['records'] = ($f['pagination']['records'] ?? '') . ' text-gray-500 dark:text-gray-400';

        return $f;
    }
}
