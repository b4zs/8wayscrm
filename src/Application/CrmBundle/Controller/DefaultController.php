<?php

namespace Application\CrmBundle\Controller;

use Application\CrmBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return new RedirectResponse($this->container->get('router')->generate('sonata_admin_dashboard'));
    }

    public function importProjectsAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $rows = $this->parseCSV();

//        foreach ($rows as $row) {
//            if (count($row) < 2 || !$row[0]) {
//                continue;
//            }
//
//            $project = new Project();
//            $project->setId($row[0]);
//            $project->setName($row[1]);
//
//            if (isset($row[3])) {
//                $project->setDescription($row[3]);
//            }
//
//            $em->persist($project);
//        }
//
//        $em->flush();
//
//        foreach ($rows as $row) {
//            if (count($row) < 2 || !$row[0] || !$row[2]) {
//                continue;
//            }
//
//            $projectId = (int)$row[0];
//            $parentId = (int)$row[2];
//
//            if($projectId == 0 || $parentId == 0) {
//                continue;
//            }
//
//            $parentReference = $em->getReference(Project::class, $row[2]);
//
//            if (!$parentReference) {
//                continue;
//            }
//
//            $project = $em->getRepository(Project::class)->findOneBy([
//                'id' => $row[0]
//            ]);
//
//            if (!$project) {
//                 continue;
//            }
//
//
//            $project->setParent($parentReference);
//            try {
//                $em->persist($project);
//                $em->flush();
//            } catch (\Exception $e) {
////                echo $e->getMessage();
//            }
//        }

    }

    private function parseCSV()
    {
        $csv = null;
        $path = $this->get('kernel')->getRootDir().'/Resources/query_result.csv';

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
