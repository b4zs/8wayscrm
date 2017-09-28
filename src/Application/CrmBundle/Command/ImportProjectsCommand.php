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
            if (count($row) < 2 || !$row[0]) {
                continue;
            }

            $project = new Project();
            $project->setId($row[0]);
            $project->setName($row[1]);

            if (isset($row[3])) {
                $project->setDescription($row[3]);
            }

            $em->persist($project);
        }

        $em->flush();

        foreach ($rows as $row) {
            if (count($row) < 2 || !$row[0] || !$row[2]) {
                continue;
            }

            $projectId = (int)$row[0];
            $parentId = (int)$row[2];

            if($projectId == 0 || $parentId == 0) {
                continue;
            }

            $parentReference = $em->getReference(Project::class, $row[2]);

            if (!$parentReference) {
                continue;
            }

            $project = $em->getRepository(Project::class)->findOneBy([
                'id' => $row[0]
            ]);

            if (!$project) {
                continue;
            }


            $project->setParent($parentReference);
            try {
                $em->persist($project);
                $em->flush();
            } catch (\Exception $e) {
            }
        }
    }

    private function parseCSV()
    {
        $csv = null;
        $path = $this->getContainer()->get('kernel')->getRootDir().'/Resources/projects.csv';

        $rows = array();

        $i = 0;
        $file = fopen($path, 'r');
        while (($data = fgetcsv($file, null, ";")) !== FALSE) {
            foreach ($data as $line) {
                $i++;

                if($i == 1) {
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
        fclose($file);

        return $rows;
    }

}