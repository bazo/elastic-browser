<?php

namespace EB\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use EB\Users\Users;
/**
 * Command
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class CreateUser extends Command
{

	/** @var Users */
	private $users;

	public function injectUsers(Users $users)
	{
		$this->users = $users;
	}

	protected function configure()
	{
		$this->setName('app:user:create')
				->setDescription('Creates a user')
				->addArgument('login', InputArgument::OPTIONAL, 'login?')
				->addArgument('password', InputArgument::OPTIONAL, 'password?');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$dialog = $this->getHelperSet()->get('dialog');

		$login = $input->getArgument('login');
		$password = $input->getArgument('password');

		if ($login === null)
		{
			$login = $dialog->ask($output, '<question>Please enter the desired login: </question>');
			if ($login === '')
			{
				$output->writeln('<error>No username given. Try again please.</error>');
				return;
			}
		}

		if ($password === null)
		{
			$password = $dialog->ask($output, sprintf('<question>Please enter the desired password for user %s: </question>', $login));

			if ($password === '')
			{
				$output->writeln('<error>No password given. Try again please.</error>');
				return;
			}
		}

		$this->users->create($login, $password);
	}
}

