<?php

namespace Lab5Com\RedisFixturesBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Lab5Com\RedisFixturesBundle\RedisFixtureInterface;

/**
 * Class RedisFixturesLoadCommand
 * @author Romain Richard
 */
class RedisFixturesLoadCommand extends Command
{
    /**
     * @var object $redisClient A Redis client supporting "set"
     */
    private $redisClient;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * RedisFixturesLoadCommand constructor.
     * @param object $redisClient
     * @param bool   $debug
     */
    public function __construct($redisClient, $debug)
    {
        $this->redisClient = $redisClient;
        $this->debug = $debug;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('lab5com:redis:fixtures-load')
            ->setDescription('Load redis fixtures')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $fixtures = $this->getFixtures();

        foreach ($fixtures as $key => $value) {
            $this->redisClient->set($key, $value);
            $this->debug("$key => $value");
        }
    }

    /**
     * @return array
     */
    private function getFixtures()
    {
        $finder = new Finder();
        $finder->files()->name('*RedisFixture.php');
        $fixtures = [];

        foreach ($finder->in('src') as $file) {
            $className = str_replace(['.php', '/'], ['', '\\'], $file->getRelativePathname());

            if (class_exists($className)) {
                $fixture = new $className();

                if ($fixture instanceof RedisFixtureInterface) {
                    $fixtures = array_merge($fixtures, $fixture->getData());
                    $this->debug("Import Redis data from $className");
                }
            }
        }

        return $fixtures;
    }

    /**
     * @param string $message
     */
    private function debug($message)
    {
        if ($this->debug) {
            $this->output->writeln($message);
        }
    }
}
