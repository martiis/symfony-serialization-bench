<?php

namespace App\Command;

use App\Model\Author;
use App\Model\Book;
use App\Model\Page;
use Nelmio\Alice\DataLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class BenchSymfonySerializerCommand extends Command
{
    protected static $defaultName = 'bench:symfony-serializer';

    private $loader;
    private $serializer;

    public function __construct(DataLoaderInterface $loader, SerializerInterface $serializer)
    {
        $this->loader = $loader;
        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('object-count', InputArgument::OPTIONAL, '', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectCount = (int) $input->getArgument('object-count');

        $io = new SymfonyStyle($input, $output);
        $io->comment('Generating objects ...');

        $objectSet = $this->loader->loadData([
            Author::class => [
                'author{1..'.$objectCount.'}' => [
                    '__construct' => ['<intval(<current()>)>'],
                    'name' => '<name()>',
                ],
            ],
            Page::class => [
                'page{1..'.$objectCount.'}' => [
                    '__construct' => ['<intval(<current()>)>'],
                    'content' => '<text()>',
                    'number' => '<intval(<current()>)>',
                ],
            ],
            Book::class => [
                'book{1..'.$objectCount.'}' => [
                    '__construct' => ['<intval(<current()>)>'],
                    'title' => '<name()>',
                    'author' => '@author<current()>',
                    'pages' => '<numberBetween(5, 15)>x @page*',
                    'releasedAt' => '<date_create()>',
                ],
            ],
        ]);

        $books = array_filter($objectSet->getObjects(), function ($object): bool {
            return $object instanceof Book;
        });
        $stopwatch = new Stopwatch();

        $io->comment('Serializing ...');
        $stopwatch->start('serialize');
        $serializedObjects = $this->serializer->serialize($books, 'json', ['groups' => 'Bench']);
        $serializeEvent = $stopwatch->stop('serialize');

        $io->comment('Deserializing ...');
        $stopwatch->start('deserialize');
        $this->serializer->deserialize($serializedObjects, Book::class . '[]', 'json', ['groups' => 'Bench']);
        $deserializeEvent = $stopwatch->stop('deserialize');

        $io->comment('Done.');

        $io->table(
            ['Event', 'Duration', 'Memory'],
            [
                ['Serialize', $serializeEvent->getDuration() . ' ms', $serializeEvent->getMemory() . ' bytes'],
                ['Deserialize', $deserializeEvent->getDuration() . ' ms', $deserializeEvent->getMemory() . ' bytes'],
            ]
        );
    }
}
