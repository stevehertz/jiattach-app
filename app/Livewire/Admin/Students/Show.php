<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use App\Models\User;

class Show extends Component
{
    public User $student;

    public function render()
    {
        return view('livewire.admin.students.show');
    }
}
