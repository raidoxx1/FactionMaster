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

namespace ShockedPlot7560\FactionMaster\Route\Faction;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use ShockedPlot7560\FactionMaster\API\MainAPI;
use ShockedPlot7560\FactionMaster\Button\ButtonFactory;
use ShockedPlot7560\FactionMaster\Button\Collection\Faction\ViewMembersCollection;
use ShockedPlot7560\FactionMaster\Database\Entity\UserEntity;
use ShockedPlot7560\FactionMaster\Route\Route;
use ShockedPlot7560\FactionMaster\Utils\Utils;

class ViewFactionMembers implements Route {

    const SLUG = "viewFactionMembers";

    public $PermissionNeed = [];

    /** @var \ShockedPlot7560\FactionMaster\Button\ButtonCollection */
    private $Collection;
    /** @var UserEntity */
    private $UserEntity;

    public function getSlug(): string
    {
        return self::SLUG;
    }

    public function __invoke(Player $player, UserEntity $User, array $UserPermissions, ?array $params = null)
    {
        $this->UserEntity = $User;
        $Faction = MainAPI::getFactionOfPlayer($player->getName());
        $this->Collection = ButtonFactory::get(ViewMembersCollection::SLUG)->init($player, $User, $Faction);
        $message = "";
        if (isset($params[0])) $message = $params[0];
        if (count($Faction->members) == 0) $message .= Utils::getText($this->UserEntity->name, "NO_MEMBERS");
        
        $menu = $this->membersListMenu($message);
        $player->sendForm($menu);
    }

    public function call(): callable
    {
        $Collection = $this->Collection;
        return function (Player $player, $data) use ($Collection) {
            if ($data === null) return;
            $Collection->process($data, $player);
            return;
        };
    }

    private function membersListMenu(string $message = "") : SimpleForm {
        $menu = new SimpleForm($this->call());
        $menu = $this->Collection->generateButtons($menu, $this->UserEntity->name);
        $menu->setTitle(Utils::getText($this->UserEntity->name, "MEMBERS_LIST_TITLE"));
        if ($message !== "") $menu->setContent($message);
        return $menu;
    }

}