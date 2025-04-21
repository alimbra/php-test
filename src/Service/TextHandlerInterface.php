<?php

namespace Service;

interface TextHandlerInterface
{
    public function canComputeText(array $data): bool;
    public function computeText($text, array $data): string;
}