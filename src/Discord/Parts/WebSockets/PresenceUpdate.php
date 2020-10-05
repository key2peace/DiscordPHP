<?php

/*
 * This file is apart of the DiscordPHP project.
 *
 * Copyright (c) 2016-2020 David Cole <david.cole1340@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\Parts\WebSockets;

use Carbon\Carbon;
use Discord\Helpers\Collection;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Role;
use Discord\Parts\Part;
use Discord\Parts\User\Member;
use Discord\Parts\User\Activity;
use Discord\Parts\User\User;

/**
 * A PresenceUpdate part is used when the `PRESENCE_UPDATE` event is fired on the WebSocket. It contains
 * information about the users presence suck as their status (online/away) and their current game.
 *
 * @property Member                       $member The member that the presence update affects.
 * @property User                         $user The user that the presence update affects.
 * @property Guild                        $guild The guild that the presence update affects.
 * @property string                       $guild_id The unique identifier of the guild that the presence update affects.
 * @property string                       $status The updated status of the user.
 * @property Activity                     $game The updated game of the user.
 */
class PresenceUpdate extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = ['user', 'game', 'guild_id', 'status', 'activities', 'client_status'];

    /**
     * Gets the member attribute.
     *
     * @return Member
     */
    protected function getMemberAttribute(): ?Member
    {
        if (isset($this->attributes['user']) && $this->guild) {
            return $this->guild->members->get('id', $this->attributes['user']->id);
        }

        return null;
    }

    /**
     * Gets the user attribute.
     *
     * @return User       The user that had their presence updated.
     * @throws \Exception
     */
    protected function getUserAttribute(): ?User
    {
        if ($user = $this->discord->users->get('id', $this->attributes['user']->id)) {
            return $user;
        }

        return $this->factory->create(User::class, (array) $this->attributes['user'], true);
    }

    /**
     * Gets the guild attribute.
     *
     * @return Guild The guild that the user was in.
     */
    protected function getGuildAttribute(): Guild
    {
        return $this->discord->guilds->get('id', $this->guild_id);
    }

    /**
     * Gets the game attribute.
     *
     * @return ?Activity The game attribute.
     */
    protected function getGameAttribute(): ?Part
    {
        if (! isset($this->attributes['game'])) {
            return null;
        }

        return $this->factory->create(Activity::class, (array) $this->attributes['game'], true);
    }
}
