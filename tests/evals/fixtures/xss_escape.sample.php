<?php

declare(strict_types=1);

namespace App\View;

class Renderizador
{
    public function renderizarTexto(string $entrada): string
    {
        return htmlspecialchars($entrada, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
