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
namespace ShockedPlot7560\FactionMaster\Leaderboard;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use ShockedPlot7560\FactionMaster\FactionMaster as Main;
use ShockedPlot7560\FactionMaster\Utils\Leaderboard;

abstract class EntityLeaderboard {
	protected $main;

	public function __construct(Main $main) {
		$this->main = $main;
	}

	abstract public function getSlug(): string;

	abstract public function getSqlQuery(): string;

	abstract public function getConfig(): Config;

	/**
	 * @param Player[]|null $players
	 */
	abstract public function place(Leaderboard $leaderboard, ?array $players = null): void;
}