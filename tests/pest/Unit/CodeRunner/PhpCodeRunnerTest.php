<?php

declare(strict_types=1);

namespace Tests\Unit\CodeExecutor;

use PHPUnit\Framework\TestCase;
use Prosopo\Views\PrivateClasses\CodeRunner\PhpCodeRunner;

class PhpCodeRunnerTest extends TestCase
{
    public function testExecutesCodeWithoutArguments(): void
    {
        // given
        $executor = new PhpCodeRunner();
        $code = '<?php global $output; $output = "Hello, World!";';

        // when
        global $output;
        $executor->runCode($code, []);

        // then
        $this->assertSame("Hello, World!", $output);
    }

    public function testExecutesCodeWithArguments(): void
    {
        // given
        $executor = new PhpCodeRunner();
        $code = '<?php global $result; $result = $arg1 + $arg2;';
        $arguments = ['arg1' => 10, 'arg2' => 20];

        // when
        global $result;
        $executor->runCode($code, $arguments);

        // then
        $this->assertSame(30, $result);
    }

    public function testExecutesCodeWithEchoStatement(): void
    {
        // given
        $executor = new PhpCodeRunner();
        $code = '<?php echo "Hello, World!";';

        // when
         ob_start();
         $executor->runCode($code, []);
         $response = ob_get_clean();

        // apply
        $this->assertSame("Hello, World!", $response);
    }
}
