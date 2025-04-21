<?php

namespace Service;

use ApplicationContext;
use User;

readonly class UserHandler implements TextHandlerInterface
{
    const string USER = 'user';

    public function __construct(private ApplicationContext $applicationContext)
    {
    }

    public function canComputeText(array $data): bool
    {
        return (isset($data[self::USER])  and ($data[self::USER]  instanceof User));
    }

    public function computeText($text, array $data): string
    {
        $user = $this->applicationContext->getCurrentUser();

        return str_replace(
            '[user:first_name]',
            ucfirst(mb_strtolower($user->firstname)),
            $text
        );
    }
}
