<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\CurlHttpClient;


#[AsCommand(
    name: 'app:download-users',
    description: 'Add a short description for your command',
)]
class DownloadUsersCommand extends Command
{

    const DEFAULT_ADDRESS = 'https://reqres.in/api/users?page=2'; 

    private $em;
    private $http_client;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->http_client = new CurlHttpClient();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this -> addOption('address', null, InputOption::VALUE_OPTIONAL, 'Custom API address', self::DEFAULT_ADDRESS);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Setting NONE generator type to allow setting custom user ID's
        $metadata = $this->em->getClassMetaData(User::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $io = new SymfonyStyle($input, $output);
        $address = $input->getOption('address');
        $io->note(sprintf('API address is: %s', $address));    
        try 
        {    
            $response = $this->http_client->request('GET', $address)->getContent();
            $data = json_decode($response, true);
            foreach ($data['data'] as $downloaded_user)
            {
                $user = new User();
                $user->setID($downloaded_user['id']);
                $user->setFirstName($downloaded_user['first_name']);
                $user->setLastName($downloaded_user['last_name']);
                $user->setEmail($downloaded_user['email']);
                $user->setAvatar($downloaded_user['avatar']);
                $this->em->persist($user);
            }
        } 
        catch (Exception $e)
        {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
        $this->em->flush($user);
        $io->success("Users successfully downloaded");

        return Command::SUCCESS;
    }
}
