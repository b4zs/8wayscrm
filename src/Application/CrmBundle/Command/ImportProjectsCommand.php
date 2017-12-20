<?php


namespace Application\CrmBundle\Command;


use Application\CrmBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ImportProjectsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('import:projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $rows = $this->parseCSV();

        foreach ($rows as $row) {
            $project = new Project();
            $project->setId($row[0]);
            $project->setName($row[1]);

            $em->persist($project);
        }

        $em->flush();

        $counter = 0;
        $sizeOfRows = count($rows);

        foreach ($rows as $row) {
            $counter++;
            $projectId = $row[0];
            $parentId = $row[2];

            $output->writeln("id: {$projectId} - parent: {$parentId}");

            if($parentId == 'NULL') {
                $output->writeln("no parent");
                continue;
            }

            $parent = $em->getRepository(Project::class)->findOneBy([
                'id' => (int)$parentId
            ]);

            if (!$parent) {
                $output->writeln("does not found the parent");
                continue;
            }

            $project = $em->getRepository(Project::class)->findOneBy([
                'id' => (int)$projectId
            ]);

            if (!$project) {
                $output->writeln("does not found the project");
                continue;
            }


            $project->setParent($parent);

            try {
                $em->persist($project);
                $em->flush();
                $output->writeln("{$counter}/{$sizeOfRows} item has been loaded..");
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }
        }


    }

    private function parseCSV()
    {
        $csv = null;
        $path = $this->getContainer()->get('kernel')->getRootDir().'/Resources/query_result.csv';

        $rows = array();

        $i = 0;
        $file = fopen($path, 'r');
        while (($data = fgetcsv($file, null, ";")) !== FALSE) {
            foreach ($data as $line) {
                $i++;

                if ($i == 1) {
                    continue;
                }

                $lineArray = explode(',', $line);

                $f = 0;
                $finalDataArray = [];

                foreach ($lineArray as $dataRow) {
                    $finalDataArray[] = $dataRow;
                    $f++;
                }
                $rows[] = $finalDataArray;
            }
        }
        fclose($file);

        return $rows;
    }

}