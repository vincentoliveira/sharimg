<?php

namespace Sharimg\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sharimg:import:twitter')
            ->setDescription('Import twitter data into sharimg')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Twitter account username'
            )
            ->addOption(
               'timeline',
               'user',
               InputOption::VALUE_OPTIONAL,
               '"user" or "home" timeline (default "user")'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        try {
            $timeline = $input->getArgument('timeline');
        } catch(\Exception $e) {
            $timeline = "user";
        }
        
        if ($timeline !== 'home') {
            $timeline = 'user';
        }

        $output->writeLn("Start import from Twiter with...");
        $output->writeLn("\t username: $username");
        $output->writeLn("\t timeline: $timeline");
        $output->writeLn("");
        
        $container = $this->getContainer();
        $twitterManager = $container->get('sharimg_import.twitter_api_manager');
        
        if ($timeline !== 'home') {
            $timeline = $twitterManager->getHomeTimeline($username);
        } else {
            $timeline = $twitterManager->getUserTimeline($username);  
        }
        
        $contentHandler = $container->get('sharimg_content.content_form_handler');
        $statused = $container->getParameter('content.status_id');
        $defaultStatusId = $statused['visible'];
        
        foreach ($timeline as $tweet) {
            $isValid = $contentHandler->isValid($tweet);
            if ($isValid === true) {
                $tweet['status_id'] = $defaultStatusId;
                if ($contentHandler->hydrateEntity($tweet)) {
                    $output->writeLn('"'.$tweet['description'].'" has been imported');
                }
            }
        }
        
        $output->writeLn("End import.");
    }
}