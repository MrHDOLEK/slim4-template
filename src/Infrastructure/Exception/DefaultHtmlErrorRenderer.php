<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Slim\Error\Renderers\HtmlErrorRenderer as HtmlErrorRendererBase;
use Slim\Exception\HttpNotFoundException;
use Twig\Environment;

class DefaultHtmlErrorRenderer extends HtmlErrorRendererBase
{
    protected string $defaultErrorTitle = "Waw, this is embarrassing";

    public function __construct(
        private readonly Environment $twig,
    ) {}

    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        if ($exception instanceof HttpNotFoundException) {
            $title = "Lost your way?";
            $content = "Sorry, we can't find that page...";

            return $this->twig->render("error.html.twig", [
                "title" => $title,
                "content" => $content,
            ]);
        }

        return parent::__invoke($exception, $displayErrorDetails);
    }
}
