<?php


namespace Application\CrmBundle\Command;


use Application\CrmBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ImportProjectsCommand extends ContainerAwareCommand
{
    private $csvParsingOptions = array(
        'finder_in' => 'app/Resources/',
        'finder_name' => 'projects.csv',
        'ignoreFirstLine' => true
    );

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('import:projects')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csv = $this->parseCSV();

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        foreach ($csv as $row) {
            $output->writeln($row['name']);
            $project = new Project();
            $project->setName($row['name']);
            $project->setParent($row['parent']);

            $em->persist($project);
        }

        $em->flush();
    }

    private function parseCSV()
    {
        $csv = null;
        $ignoreFirstLine = $this->csvParsingOptions['ignoreFirstLine'];
        $finder = new Finder();
        $finder->files()
            ->in($this->csvParsingOptions['finder_in'])
            ->name($this->csvParsingOptions['finder_name']);

        foreach ($finder as $file) {
            $csv = $file;
        }

        $rows = array();

        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                $i++;
                if ($ignoreFirstLine && $i == 1) {
                    continue;
                }
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }

}