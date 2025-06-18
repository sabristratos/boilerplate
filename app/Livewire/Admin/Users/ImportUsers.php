<?php

namespace App\Livewire\Admin\Users;

use App\Imports\UsersImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Import Users')]
class ImportUsers extends Component
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new UsersImport, $this->file);
            Flux::toast(
                text: __('Users imported successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            return redirect()->route('admin.users.index');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                foreach ($failure->errors() as $error) {
                    Flux::toast(
                        text: $error,
                        heading: __('Validation Error on row :row', ['row' => $failure->row()]),
                        variant: 'danger'
                    );
                }
            }
        }
        return null;
    }

    public function render()
    {
        return view('livewire.admin.users.import-users');
    }
}
