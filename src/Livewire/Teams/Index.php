<?php

namespace App\Livewire\Teams;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.teams.index')
            ->layout('layouts.app', [
                'title' => 'Teams',
            ]);
    }
}

