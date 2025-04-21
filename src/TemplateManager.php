<?php

use Service\TextHandlers;

readonly class TemplateManager
{
    public function __construct(
        private TextHandlers $container
    ) {}

    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);

        $replaced->subject = $this->container->computeText($replaced->subject, $data);
        $replaced->content = $this->container->computeText($replaced->content, $data);

        return $replaced;
    }
}
