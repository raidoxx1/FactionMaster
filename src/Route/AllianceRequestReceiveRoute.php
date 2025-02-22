<?php

/*
 *
 *      ______           __  _                __  ___           __
 *     / ____/___ ______/ /_(_)___  ____     /  |/  /___ ______/ /____  _____
 *    / /_  / __ `/ ___/ __/ / __ \/ __ \   / /|_/ / __ `/ ___/ __/ _ \/ ___/
 *   / __/ / /_/ / /__/ /_/ / /_/ / / / /  / /  / / /_/ (__  ) /_/  __/ /
 *  /_/    \__,_/\___/\__/_/\____/_/ /_/  /_/  /_/\__,_/____/\__/\___/_/
 *
 * FactionMaster - A Faction plugin for PocketMine-MP
 * This file is part of FactionMaster
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @author ShockedPlot7560
 * @link https://github.com/ShockedPlot7560
 *
 *
 */

namespace ShockedPlot7560\FactionMaster\Route;

use pocketmine\player\Player;
use ShockedPlot7560\FactionMaster\API\MainAPI;
use ShockedPlot7560\FactionMaster\Button\Collection\AllianceRequestReceiveCollection;
use ShockedPlot7560\FactionMaster\Button\Collection\CollectionFactory;
use ShockedPlot7560\FactionMaster\Database\Entity\InvitationEntity;
use ShockedPlot7560\FactionMaster\Database\Entity\UserEntity;
use ShockedPlot7560\FactionMaster\Permission\PermissionIds;
use ShockedPlot7560\FactionMaster\Utils\Utils;
use Vecnavium\FormsUI\SimpleForm;
use function count;
use function is_string;

class AllianceRequestReceiveRoute extends RouteBase implements Route {
	const SLUG = "allianceRequestReceiveRoute";

	/** @var InvitationEntity[] */
	private $invitations;

	public function getSlug(): string {
		return self::SLUG;
	}

	public function getPermissions(): array {
		return [
			PermissionIds::PERMISSION_ACCEPT_ALLIANCE_DEMAND,
			PermissionIds::PERMISSION_REFUSE_ALLIANCE_DEMAND
		];
	}

	public function getBackRoute(): ?Route {
		return RouterFactory::get(AllianceOptionRoute::SLUG);
	}

	/** @return InvitationEntity[] */
	protected function getInvitations(): array {
		return $this->invitations;
	}

	public function __invoke(Player $player, UserEntity $userEntity, array $userPermissions, ?array $params = null) {
		$this->init($player, $userEntity, $userPermissions, $params);

		$this->invitations = MainAPI::getInvitationsByReceiver($this->getFaction()->getName(), InvitationEntity::ALLIANCE_INVITATION);
		$this->setCollection(CollectionFactory::get(AllianceRequestReceiveCollection::SLUG)->init($this->getPlayer(), $this->getUserEntity(), $this->getInvitations()));

		$message = "";
		if (isset($params[0]) && is_string($params[0])) {
			$message = $params[0];
		}
		if (count($this->getInvitations()) == 0) {
			$message .= Utils::getText($this->getUserEntity()->getName(), "NO_PENDING_REQUEST");
		}

		$player->sendForm($this->getForm($message));
	}

	public function call(): callable {
		return function (Player $player, $data) {
			if ($data === null) {
				return;
			}
			$this->getCollection()->process($data, $player);
			return;
		};
	}

	protected function getForm(string $message = ""): SimpleForm {
		$menu = new SimpleForm($this->call());
		$menu = $this->getCollection()->generateButtons($menu, $this->getUserEntity()->getName());
		$menu->setTitle(Utils::getText($this->getUserEntity()->getName(), "REQUEST_LIST_TITLE"));
		if ($message !== "") {
			$menu->setContent($message);
		}
		return $menu;
	}
}