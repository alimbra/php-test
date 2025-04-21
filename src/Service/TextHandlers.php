<?php

namespace Service;

readonly class TextHandlers
{

    /**
     * @param TextHandlerInterface[] $textHandlers
     */
    public function __construct(private array $textHandlers)
    {
    }

    public function computeText(string $text, array $data): string
    {
        foreach ($this->textHandlers as $textHandler) {
            if ($textHandler->computeText($text, $data)) {
                $text = $textHandler->computeText($text, $data);
            }
        }

        return $text;
    }
}
