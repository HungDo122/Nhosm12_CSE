<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isClubManager();
    }

    public function view(User $user, Event $event)
    {
        if ($user->isAdmin()) return true;
        $clubIds = $user->managedClubs->pluck('id')->toArray();
        return in_array($event->club_id, $clubIds);
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isClubManager();
    }

    public function update(User $user, Event $event)
    {
        if ($user->isAdmin()) return true;
        $clubIds = $user->managedClubs->pluck('id')->toArray();
        return in_array($event->club_id, $clubIds);
    }

    public function delete(User $user, Event $event)
    {
        return $this->update($user, $event);
    }

    public function approve(User $user, Event $event)
    {
        return $user->isAdmin();
    }
    
    public function reject(User $user, Event $event)
    {
        return $user->isAdmin();
    }
}
