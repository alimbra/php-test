<?php

use PHPUnit\Framework\TestCase;
use Service\QuoteHandler;
use Service\TextHandlers;
use Service\UserHandler;


require_once __DIR__ . '/../src/Entity/Destination.php';
require_once __DIR__ . '/../src/Entity/Quote.php';
require_once __DIR__ . '/../src/Entity/Site.php';
require_once __DIR__ . '/../src/Entity/Template.php';
require_once __DIR__ . '/../src/Entity/User.php';
require_once __DIR__ . '/../src/Helper/SingletonTrait.php';
require_once __DIR__ . '/../src/Context/ApplicationContext.php';
require_once __DIR__ . '/../src/Repository/Repository.php';
require_once __DIR__ . '/../src/Repository/DestinationRepository.php';
require_once __DIR__ . '/../src/Repository/QuoteRepository.php';
require_once __DIR__ . '/../src/Repository/SiteRepository.php';
require_once __DIR__ . '/../src/TemplateManager.php';
require_once __DIR__ . '/../src/Service/TextHandlerInterface.php';
require_once __DIR__ . '/../src/Service/TextHandlers.php';
require_once __DIR__ . '/../src/Service/QuoteHandler.php';
require_once __DIR__ . '/../src/Service/UserHandler.php';

class TemplateManagerTest extends TestCase
{

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $faker = \Faker\Factory::create();

        $destinationId                  = $faker->randomNumber();
        $expectedDestination = DestinationRepository::getInstance()->getById($destinationId);
        $expectedUser        = ApplicationContext::getInstance()->getCurrentUser();

        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $destinationId, $faker->date());

        $template = new Template(
            1,
            'Votre livraison à [quote:destination_name]',
            "
Bonjour [user:first_name],

Merci de nous avoir contacté pour votre livraison à [quote:destination_name].

Bien cordialement,

L'équipe de Shipper
");
        $textHandlers = new TextHandlers(
            [
                new QuoteHandler(
                    QuoteRepository::getInstance(),
                    SiteRepository::getInstance(),
                    DestinationRepository::getInstance()
                ),
                new UserHandler(
                    ApplicationContext::getInstance()
                ),
            ]
        );
        $templateManager = new TemplateManager($textHandlers);

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre livraison à ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Merci de nous avoir contacté pour votre livraison à " . $expectedDestination->countryName . ".

Bien cordialement,

L'équipe de Shipper
", $message->content);
    }
}
