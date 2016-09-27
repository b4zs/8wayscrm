<?php
namespace Application\CrmBundle\Command;


use Application\CrmBundle\Enum\PersonTitleEnum;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContactTitleFixCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:fix:note:contact-title');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentTitles = $this->getPdo()->query('SELECT DISTINCT crm__contact.title FROM `crm__contact` WHERE crm__contact.title IS NOT NULL')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($currentTitles as $currentTitle) {
            $stmt = $this->getPdo()->prepare('SELECT * FROM crm__contact WHERE title = :title');
            $stmt->bindParam(':title', $currentTitle);
            $stmt->execute();
            $records = $stmt->fetchAll();

            foreach ($records as $record) {
                $title = $this->getValidTitle($record['title']);
                $role  = $record['role'];
                if(null === $title) {
                    if(null === $record['role']) {
                        $role = $record['title'];
                    } else {
                        $role = $record['role'] . ', ' . $record['title'];
                    }
                }

                $updStmt = $this->getPdo()->prepare('UPDATE crm__contact SET title = :title, role = :role WHERE id = :id');
                $updStmt->bindParam(':title', $title);
                $updStmt->bindParam(':role', $role);
                $updStmt->bindParam(':id', $record['id']);
                $updStmt->execute();
            }
        }
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     */
    protected function getDoctrine() {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return \PDO
     */
    protected function getPdo() {
        return $this->getDoctrine()->getConnection();
    }

    /**
     * @param $currentTitle
     * @return mixed|null
     */
    protected function getValidTitle($currentTitle) {
        $titleMap = array(
            'M'  => PersonTitleEnum::TITLE_MR,
            'Mr' => PersonTitleEnum::TITLE_MR,
            'M.' => PersonTitleEnum::TITLE_MR,
            'Mr.' => PersonTitleEnum::TITLE_MR,
            'Madame' => PersonTitleEnum::TITLE_MRS,
            'Mrs' => PersonTitleEnum::TITLE_MRS,
            'Me' => PersonTitleEnum::TITLE_MRS,
        );

        if(!array_key_exists($currentTitle, $titleMap)) {
            return null;
        }

        return $titleMap[$currentTitle];
    }


}