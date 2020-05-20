<?php
declare(strict_types=1);

namespace OCA\DeckTransferOwnership\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeckTransferOwnership extends Command {

	protected function configure() {
		$this
			->setName('deck:transfer-ownership')
			->setDescription('Change owner of deck entities')
			->addArgument(
				'owner',
				InputArgument::REQUIRED,
				'Owner uid'
			)
            ->addArgument(
                'newOwner',
                InputArgument::REQUIRED,
                'New owner uid'
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$owner = $input->getArgument('owner');
		$newOwner = $input->getArgument('newOwner');
        $db = \OC::$server->getDatabaseConnection();

        $output->writeln("Transfer deck entities from $owner to $newOwner");
        $params = [
            'owner' => $owner,
            'newOwner' => $newOwner
        ];

        $output->writeln('update oc_deck_assigned_users');
        $stmt = $db->prepare('UPDATE `oc_deck_assigned_users` SET `participant` = replace(`participant`, :owner, :newOwner)');
        $stmt->execute($params);

        $output->writeln('update oc_deck_attachment');
        $stmt = $db->prepare('UPDATE `oc_deck_attachment` SET `created_by` = replace(`created_by`, :owner, :newOwner)');
        $stmt->execute($params);

        $output->writeln('update oc_deck_boards');
        $stmt = $db->prepare('UPDATE `oc_deck_boards` SET `owner` = replace(`owner`, :owner, :newOwner)');
        $stmt->execute($params);

        $output->writeln('update oc_deck_board_acl');
        $stmt = $db->prepare('UPDATE `oc_deck_board_acl` SET `participant` = replace(`participant`, :owner, :newOwner)');
        $stmt->execute($params);

        $output->writeln('update oc_deck_cards');
        $stmt = $db->prepare('UPDATE `oc_deck_cards` SET `owner` = replace(`owner`, :owner, :newOwner)');
        $stmt->execute($params);

        $output->writeln("Transfer deck entities from $owner to $newOwner completed");
    }

}
