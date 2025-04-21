<?php

namespace Service;

use DestinationRepository;
use Quote;
use QuoteRepository;
use SiteRepository;

class QuoteHandler implements TextHandlerInterface
{
    const string QUOTE = 'quote';

    private QuoteRepository $quoteRepository;
    private SiteRepository $siteRepository;
    private DestinationRepository $destinationRepository;

    public function __construct(
        QuoteRepository $quoteRepository,
        SiteRepository $siteRepository,
        DestinationRepository $destinationRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->siteRepository = $siteRepository;
        $this->destinationRepository = $destinationRepository;
    }
    public function canComputeText(array $data): bool
    {
        return isset($data[self::QUOTE]) and $data[self::QUOTE] instanceof Quote;
    }

    public function computeText($text, array $data): string
    {
        $quote = $data[self::QUOTE];

        //Normaly we should check the existance of the quote, destination and the site but
        //No need for verification here since we know that we will get an entity as a return (FAKER)
        $quoteEntity = $this->quoteRepository->getById($quote->id);
        $destination = $this->destinationRepository->getById($quote->destinationId);
        $site = $this->siteRepository->getById($quote->siteId);

        $replacements = [
            '[quote:summary_html]'     => Quote::renderHtml($quoteEntity),
            '[quote:summary]'          => Quote::renderText($quoteEntity),
            '[quote:destination_name]' => $destination->countryName,
            '[quote:destination_link]' => $site->url . '/' . $destination->countryName . '/quote/' . $quoteEntity->id,
        ];

        foreach ($replacements as $tag => $value) {
            $text = str_replace($tag, $value, $text);
        }

        return $text;
    }
}