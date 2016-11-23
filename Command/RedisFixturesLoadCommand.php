<?php

namespace Lab5Com\RedisFixturesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Lab5Com\RedisFixturesBundle\RedisFixtureInterface;

/**
 * Class RedisFixturesLoadCommand
 * @author Romain Richard
 */
class RedisFixturesLoadCommand extends ContainerAwareCommand
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
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

    }

    /**
     * Configure the command name & description
     */
    protected function configure()
    {
        $this
            ->setName('lab5com:redis-fixtures-load')
            ->setDescription('Load redis fixtures')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setUp($output);
        $fixtures = $this->getFixtures();

        if (empty($fixtures)) {
            $output->writeln('Nothing to load');
        }

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
     * @param OutputInterface $output
     * @throws \Exception
     */
    private function setUp(OutputInterface $output)
    {
        $redisClient = $this->getContainer()->getParameter('lab5com.redis_fixtures.client');

        if (!$this->getContainer()->has($redisClient)) {
            throw new \Exception(
                'The service "snc_redis.default" is not available.'."\n".
                'Configure lab5com_redis_fixtures.client with your own redis client or install SncRedisBundle'
            );
        }

        $this->redisClient = $this->getContainer()->get($redisClient);
        $this->debug = $this->getContainer()->getParameter('lab5com.redis_fixtures.debug');
        $this->output = $output;
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
