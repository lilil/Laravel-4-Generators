<?php

namespace spec\Lilil\Generators;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Lilil\Generators\Compilers\TemplateCompiler;
use Lilil\Generators\Filesystem\Filesystem;

class SchemaCreatorSpec extends ObjectBehavior {

    public function let(Filesystem $file, TemplateCompiler $compiler)
    {
        $this->beConstructedWith($file, $compiler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Lilil\Generators\SchemaCreator');
    }

}
