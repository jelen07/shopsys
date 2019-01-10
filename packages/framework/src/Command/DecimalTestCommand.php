<?php

namespace Shopsys\FrameworkBundle\Command;

use Litipk\BigNumbers\Decimal as LitipkDecimal;
use Shopsys\FrameworkBundle\Component\Decimal\Decimal as ShopsysDecimal;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecimalTestCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:decimal:test';

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterations = 10000;
        $random = [];
        for ($i = 0; $i < $iterations; $i++) {
            $random[$i] = [rand(0, 10000), rand(-10000, 10000), rand(10, 1000), rand(0, 10000) / 100];
        }

        $primitiveResults = $this->testPrimitive($output, $iterations, $random);
        $litipkDecimalResults = $this->testLitipkDecimal($output, $iterations, $random);
        $shopsysDecimalResults = $this->testShopsysDecimal($output, $iterations, $random);

        for ($i = 0; $i < 10; $i++) {
            $output->writeln(\sprintf('Result #%d: (%s + %s) / %s * %s = %s (%s), %s (litipk), %s (shopsys)', $i + 1, $random[$i][0], $random[$i][1], $random[$i][2], $random[$i][3], $primitiveResults[$i], gettype($primitiveResults[$i]), $litipkDecimalResults[$i], $shopsysDecimalResults[$i]));
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $iterations
     * @param int[][]|float[][] $random
     * @return int[]|float[]
     */
    protected function testPrimitive(OutputInterface $output, int $iterations, array $random): array
    {
        $startTime = microtime(true);

        $results = [];
        for ($i = 0; $i < $iterations; $i++) {
            $results[$i] = ($random[$i][0] + $random[$i][1]) / $random[$i][2] * $random[$i][3];
        }
        $output->writeln(\sprintf('Tested %d iterations of float computations. Took %.5f s.', $iterations, microtime(true) - $startTime));

        return $results;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $iterations
     * @param int[][]|float[][] $random
     * @return \Litipk\BigNumbers\Decimal[]
     */
    protected function testLitipkDecimal(OutputInterface $output, int $iterations, array $random): array
    {
        $initTime = microtime(true);

        /** @var \Litipk\BigNumbers\Decimal[][] $randomObj */
        $randomObj = [];
        for ($i = 0; $i < $iterations; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $randomObj[$i][$j] = LitipkDecimal::create($random[$i][$j]);
            }
        }

        $startTime = microtime(true);

        $results = [];
        for ($i = 0; $i < $iterations; $i++) {
            $results[$i] = $randomObj[$i][0]->add($randomObj[$i][1])->div($randomObj[$i][2])->mul($randomObj[$i][3]);
        }
        $output->writeln(\sprintf('Tested %d iterations of Litipk\BigNumbers\Decimal computations. Took %.5f s. (Instantiation took %.5f s.)', $iterations, microtime(true) - $startTime, $startTime - $initTime));

        return $results;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $iterations
     * @param int[][]|float[][] $random
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal[]
     */
    protected function testShopsysDecimal(OutputInterface $output, int $iterations, array $random): array
    {
        $initTime = microtime(true);

        /** @var \Shopsys\FrameworkBundle\Component\Decimal\Decimal[][] $randomObj */
        $randomObj = [];
        for ($i = 0; $i < $iterations; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $randomObj[$i][$j] = ShopsysDecimal::create($random[$i][$j]);
            }
        }

        $startTime = microtime(true);

        $results = [];
        for ($i = 0; $i < $iterations; $i++) {
            $results[$i] = $randomObj[$i][0]->add($randomObj[$i][1])->divide($randomObj[$i][2])->multiply($randomObj[$i][3]);
        }

        $output->writeln(\sprintf('Tested %d iterations of Shopsys\FrameworkBundle\Component\Decimal\Decimal computations. Took %.5f s. (Instantiation took %.5f s.)', $iterations, microtime(true) - $startTime, $startTime - $initTime));

        return $results;
    }
}
