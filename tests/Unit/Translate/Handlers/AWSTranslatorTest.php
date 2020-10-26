<?php


namespace Twigger\Tests\Translate\Unit\Translate\Handlers;


use Aws\Command;
use Aws\Exception\AwsException;
use Aws\Result;
use Illuminate\Contracts\Container\Container;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Twigger\Tests\Translate\TestCase;
use Twigger\Translate\Translate\Handlers\AWSTranslator;

class AWSTranslatorTest extends TestCase
{

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    public $container;
    /**
     * @var TranslateClient
     */
    public $awsClient;

    protected function setUp(): void
    {
        parent::setUp();

        $container = $this->prophesize(Container::class);
        $container->make(\Aws\Translate\TranslateClient::class, Argument::any())->will(function($args) {
            $this->awsClient = new TranslateClient($args[1]['args']);
            return $this->awsClient;
        });
        $this->container = $container;
    }

    /** @test */
    public function it_can_send_a_request_for_translation_to_aws(){
        $translator = new AWSTranslator([
            'version' => 'latest',
            'region' => 'us-west-1',
            'key' => 'aws-key',
            'secret' => 'aws-secret'
        ], $this->container->reveal());

        $result = $this->prophesize(Result::class);
        $result->hasKey('TranslatedText')->willReturn(true);
        $result->get('TranslatedText')->willReturn('Translated');

        TranslateClient::translateShouldReturn($result->reveal());

        $translatedText = $translator->translate('Some Line', 'fr', 'en');

        $this->assertEquals('Translated', $translatedText);
        $this->assertEquals([
            'version' => 'latest',
            'region' => 'us-west-1',
            'credentials' => [
                'key' => 'aws-key',
                'secret' => 'aws-secret'
            ]
        ], TranslateClient::getArgs());

        $this->assertEquals([
            'SourceLanguageCode' => 'en',
            'TargetLanguageCode' => 'fr',
            'Text' => 'Some Line',
        ], TranslateClient::$translateArgs);

    }

    /** @test */
    public function it_returns_null_if_an_error_occured_and_logs(){
        $translator = new AWSTranslator([
            'version' => 'latest',
            'region' => 'us-west-1',
            'key' => 'aws-key',
            'secret' => 'aws-secret'
        ], $this->container->reveal());

        $result = $this->prophesize(Result::class);

        TranslateClient::translateShouldThrow(new AwsException('Some error message', new Command('translate')));
        $log = $this->prophesize(Logger::class);
        $log->warning('Some error message')->shouldBeCalled();
        Log::swap($log->reveal());

        $translatedText = $translator->translate('Some Line', 'fr', 'en');

        $this->assertNull($translatedText);
        $this->assertEquals([
            'version' => 'latest',
            'region' => 'us-west-1',
            'credentials' => [
                'key' => 'aws-key',
                'secret' => 'aws-secret'
            ]
        ], TranslateClient::getArgs());

        $this->assertEquals([
            'SourceLanguageCode' => 'en',
            'TargetLanguageCode' => 'fr',
            'Text' => 'Some Line',
        ], TranslateClient::$translateArgs);
    }


    /** @test */
    public function it_returns_null_if_an_error_occured_and_logs_if_log_errors_is_true(){
        $translator = new AWSTranslator([
            'version' => 'latest',
            'region' => 'us-west-1',
            'key' => 'aws-key',
            'secret' => 'aws-secret',
            'log_errors' => true
        ], $this->container->reveal());

        $result = $this->prophesize(Result::class);

        TranslateClient::translateShouldThrow(new AwsException('Some error message', new Command('translate')));
        $log = $this->prophesize(Logger::class);
        $log->warning('Some error message')->shouldBeCalled();
        Log::swap($log->reveal());

        $translatedText = $translator->translate('Some Line', 'fr', 'en');

        $this->assertNull($translatedText);
        $this->assertEquals([
            'version' => 'latest',
            'region' => 'us-west-1',
            'credentials' => [
                'key' => 'aws-key',
                'secret' => 'aws-secret'
            ]
        ], TranslateClient::getArgs());

        $this->assertEquals([
            'SourceLanguageCode' => 'en',
            'TargetLanguageCode' => 'fr',
            'Text' => 'Some Line',
        ], TranslateClient::$translateArgs);
    }

    /** @test */
    public function it_returns_null_if_an_error_occured_and_does_not_log_if_log_errors_false(){
        $translator = new AWSTranslator([
            'version' => 'latest',
            'region' => 'us-west-1',
            'key' => 'aws-key',
            'secret' => 'aws-secret',
            'log_errors' => false
        ], $this->container->reveal());

        $result = $this->prophesize(Result::class);

        TranslateClient::translateShouldThrow(new AwsException('Some error message', new Command('translate')));
        $log = $this->prophesize(Logger::class);
        $log->warning('Some error message')->shouldNotBeCalled();
        Log::swap($log->reveal());

        $translatedText = $translator->translate('Some Line', 'fr', 'en');

        $this->assertNull($translatedText);
        $this->assertEquals([
            'version' => 'latest',
            'region' => 'us-west-1',
            'credentials' => [
                'key' => 'aws-key',
                'secret' => 'aws-secret'
            ]
        ], TranslateClient::getArgs());

        $this->assertEquals([
            'SourceLanguageCode' => 'en',
            'TargetLanguageCode' => 'fr',
            'Text' => 'Some Line',
        ], TranslateClient::$translateArgs);
    }

}

class TranslateClient extends \Aws\Translate\TranslateClient
{

    public static $args;

    /**
     * @var string
     */
    public static $translateType;

    /**
     * @var \Exception|Result
     */
    public static $translateContent;

    /**
     * @var array
     */
    public static $translateArgs;

    public function __construct(array $args)
    {
        static::$args = $args;
    }

    public static function reset()
    {
        static::$args = [];
        static::$translateType = null;
        static::$translateContent = null;
        static::$translateArgs = [];
    }

    public function translateText(array $args = [])
    {
        static::$translateArgs = $args;
        if(static::$translateType === 'return') {
            return static::$translateContent;
        } else if(static::$translateType === 'throw') {
            throw static::$translateContent;
        }
        throw new \Exception('Specify return type in tests');
    }

    public static function translateShouldReturn(Result $result)
    {
        static::$translateContent = $result;
        static::$translateType = 'return';
    }

    public static function translateShouldThrow(\Exception $exception)
    {
        static::$translateContent = $exception;
        static::$translateType = 'throw';
    }

    public static function getArgs()
    {
        return static::$args;
    }

    public static function getTranslateArgs()
    {
        return static::$translateArgs;
    }


}
