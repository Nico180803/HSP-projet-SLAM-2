<?php

namespace App\Twig;

use App\Repository\FluxRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalVariablesExtension extends AbstractExtension implements GlobalsInterface
{
    private $forumRepository;
    public function __construct(FluxRepository $forumRepository)
    {
        $this->forumRepository = $forumRepository;
    }

    public function getGlobals(): array
    {
        return [
            'forum' => $this->forumRepository->findAll(),
        ];
    }
}
