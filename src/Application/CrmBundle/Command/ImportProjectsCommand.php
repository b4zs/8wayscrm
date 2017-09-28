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
            ->setName('import:projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csv = $this->parseCSV();


//
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
//
        foreach ($csv as $row) {
            if (isset($row[1]) && $row[1]) {

                $project = new Project();
                $project->setName($row[1]);
//
                $em->persist($project);
            }

//            return ;
        }
//
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
        $columns = [];

        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                foreach ($data as $line) {
                    $i++;

                    if ($i == 1) {
                        $columns = explode(',', $line);
                        $columns = array_map(
                            function ($str) {
                                return str_replace('"', '', $str);
                            },
                            $columns
                        );
                        continue;
                    }

                    $lineArray = explode(',', $line);
                    $lineArray = array_map(
                        function ($str) {
                            return str_replace('"', '', $str);
                        },
                        $lineArray
                    );

                    $f = 0;
                    $finalDataArray = [];
                    foreach ($lineArray as $dataRow) {
                        $finalDataArray[] = $dataRow;
                        $f++;
                    }
                    $rows[] = $finalDataArray;
                }
            }
            fclose($handle);
        }

        return $rows;
    }

}