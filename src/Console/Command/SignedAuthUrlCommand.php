<?php

namespace Creads\Partners\Console\Command;

use Creads\Partners\ClientFactory;
use Creads\Partners\SignedAuthenticationUrlFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SignedAuthUrlCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('signed-auth-url')
            ->setDescription('Generate a signed auth URL')
            ->addArgument(
                'email',
                InputArgument::REQUIRED
            )->addArgument(
                'organizationName',
                InputArgument::REQUIRED
            )->addArgument(
                'organizationRid',
                InputArgument::OPTIONAL
            )->addArgument(
                'firstname',
                InputArgument::OPTIONAL
            )->addArgument(
                'lastname',
                InputArgument::OPTIONAL
            )->addOption(
                'protocol',
                null,
                InputOption::VALUE_REQUIRED,
                'Set protocol to another version than default',
                SignedAuthenticationUrlFactory::RFC1_SIGNATURE_PROTOCOL
            );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (0 != $returnCode = $this->login($output)) {
            return $returnCode;
        }

        $configuration = $this->getHelperSet()->get('configuration');

        $protocol = $input->getOption('protocol');

        if (!in_array($protocol, SignedAuthenticationUrlFactory::getAvailableProtocoles())) {
            throw new \RuntimeException('Invalid value for protocol');
        }

        $signedUrl = SignedAuthenticationUrlFactory::create(
            $configuration,
            [
                'organizationRid' => $input->getArgument('organizationRid'),
                'organizationName' => $input->getArgument('organizationName'),
                'email' => $input->getArgument('email'),
                'firstname' => $input->getArgument('firstname'),
                'lastname' => $input->getArgument('lastname'),
            ],
            $protocol
        );

        $output->writeln(sprintf('<comment>%s</comment>', $signedUrl));
    }

}