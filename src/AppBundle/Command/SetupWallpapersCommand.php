<?php

namespace AppBundle\Command;

use AppBundle\Entity\Wallpaper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupWallpapersCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct( EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('app:setup-wallpapers')
            ->setDescription('Grabs all the local wallpapers and creates a Wallpaper entity for each one.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   

        $io = new SymfonyStyle($input, $output);
        
        $wallpapers = glob("%kernel.root_dir%/../web/images/*.*");
        $wallpaperCount = count($wallpapers);

        $io->title('Importing Wallpapers');
        $io->progressStart($wallpaperCount);

        $fileNames = [];

        foreach ($wallpapers as $wallpaper) {

            $wp = (new Wallpaper())
                ->setFilename($wallpaper)
                ->setSlug($wallpaper)
                ->setWidth(1080)
                ->setHeight(712)
            ;

            $this->em->persist($wp);

            $io->progressAdvance();

            $fileNames[] = [$filename];

        }

        $this->em->flush();

        $io->progressFinish();

        $io->success(sprintf('Cool, we added %d wallpapers', $wallpaperCount));

    }

}
