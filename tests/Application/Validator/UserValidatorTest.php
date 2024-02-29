<?php

declare(strict_types=1);

namespace Tests\Application\Validator;

use App\Application\Validator\UserValidator;
use App\Domain\DomainException\ValidationException;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\TestCase;
use Throwable;

class UserValidatorTest extends TestCase
{
    public function testProcessSuccess(): void
    {
        $validator = new UserValidator();

        $request = $this->createRequest(
            "POST",
            "",
            [],
            [],
            [],
            '{
              "username": "Janusz123",
              "firstName": "Janusz",
              "lastName": "Borowy"
            }',
        );
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->shouldBeCalledOnce();

        $validator->process($request, $handler->reveal());

        $this->assertTrue($validator->isValid());
    }

    public function testProcessThrowsValidationException(): void
    {
        $validator = new UserValidator();

        try {
            $request = $this->createRequest(
                "POST",
                "",
                [],
                [],
                [],
                '{
                  "firstName": "Janusz",
                  "lastName": "' . str_repeat("a", 256) . '"
                }',
            );
            $handler = $this->prophesize(RequestHandlerInterface::class);
            $handler->handle($request)->shouldNotBeCalled();

            $validator->process($request, $handler->reveal());
        } catch (Throwable $exception) {
            $this->assertInstanceOf(ValidationException::class, $exception);

            $this->assertEquals(
                ["username" => "null must be a string, null must not be empty, null must have a length between 1 and 255"],
                $exception->errors()[0]->jsonSerialize(),
            );
            $this->assertStringContainsString(
                "must have a length between 1 and 255",
                $exception->errors()[1]->jsonSerialize()["lastName"],
            );

            $this->assertCount(2, $validator->getErrors());
        }
    }
}
