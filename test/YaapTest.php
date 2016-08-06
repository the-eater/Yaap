<?php
namespace Eater;

include __DIR__ . '/../vendor/autoload.php';

use Eater\Yaap\Option;
use PHPUnit\Framework\TestCase;

class YaapTest extends TestCase
{
    public function testBasicParsing() {
        $yaap = new Yaap(['help', '-vvvv', '--topic=hot', '--output', 'json', '--flag', '--option', 'another-rest']);
        $v = $yaap->addOption(Option::INCREMENTING, 'v');
        $topic = $yaap->addOption(Option::REQUIRED, 'topic');
        $output = $yaap->addOption(Option::REQUIRED, 'output');
        $flag = $yaap->addOption(Option::FLAG, 'flag');
        $noflag = $yaap->addOption(Option::FLAG, 'nonexistentflag');
        $option = $yaap->addOption(Option::OPTIONAL, 'option');
        $rest = $yaap->parse();

        $this->assertSame($v->getValue(), 4);
        $this->assertTrue($flag->getValue());
        $this->assertFalse($noflag->getValue());
        $this->assertTrue($option->getValue());
        $this->assertSame($topic->getValue(), 'hot');
        $this->assertSame($output->getValue(), 'json');
        $this->assertSame($rest, ['help', 'another-rest']);
    }
}